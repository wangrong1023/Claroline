<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CoreBundle\Controller;

use Claroline\CoreBundle\Entity\Tool\AdminTool;
use Claroline\CoreBundle\Event\Log\LogAdminToolReadEvent;
use Claroline\CoreBundle\Event\Tool\OpenToolEvent;
use Claroline\CoreBundle\Manager\Tool\ToolManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/admin", options={"expose"=true})
 */
class AdministrationController
{
    /** @var AuthorizationCheckerInterface */
    private $authorization;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ToolManager */
    private $toolManager;

    /**
     * AdministrationController constructor.
     */
    public function __construct(
        AuthorizationCheckerInterface $authorization,
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher,
        ToolManager $toolManager
    ) {
        $this->authorization = $authorization;
        $this->eventDispatcher = $eventDispatcher;
        $this->toolManager = $toolManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Opens the administration index.
     *
     * @Route("/", name="claro_admin_index")
     * @Route("/", name="claro_admin_open")
     *
     * @throws AccessDeniedException
     *
     * @return JsonResponse
     */
    public function openAction()
    {
        $tools = $this->toolManager->getAdminToolsByRoles($this->tokenStorage->getToken()->getRoleNames());
        if (0 === count($tools)) {
            throw new AccessDeniedException();
        }

        return new JsonResponse([
            'tools' => array_values(array_map(function (AdminTool $tool) {
                return [
                    'icon' => $tool->getClass(),
                    'name' => $tool->getName(),
                ];
            }, $tools)),
        ]);
    }

    /**
     * Opens an administration tool.
     *
     * @Route("/open/{toolName}", name="claro_admin_open_tool")
     *
     * @param $toolName
     *
     * @throws AccessDeniedException
     *
     * @return JsonResponse
     */
    public function openToolAction($toolName)
    {
        $tool = $this->toolManager->getAdminToolByName($toolName);
        if (!$tool) {
            throw new NotFoundHttpException('Tool not found');
        }

        if (!$this->authorization->isGranted('OPEN', $tool)) {
            throw new AccessDeniedException();
        }

        /** @var OpenToolEvent $event */
        $event = $this->eventDispatcher->dispatch(new OpenToolEvent(), 'administration_tool_'.$toolName);

        $this->eventDispatcher->dispatch(new LogAdminToolReadEvent($toolName), 'log');

        return new JsonResponse(array_merge($event->getData(), [
            'data' => [
                'permissions' => [
                    'open' => $this->authorization->isGranted('OPEN', $tool),
                    'edit' => $this->authorization->isGranted('EDIT', $tool),
                ],
            ],
        ]));
    }

    /**
     * Lists admin tools accessible by the current user.
     *
     * @Route("/tools", name="claro_admin_tools")
     *
     * @return JsonResponse
     */
    public function listToolsAction()
    {
        $tools = $this->toolManager->getAdminToolsByRoles($this->tokenStorage->getToken()->getRoleNames());

        return new JsonResponse([
            'tools' => array_values(array_map(function (AdminTool $tool) {
                return [
                    'icon' => $tool->getClass(),
                    'name' => $tool->getName(),
                ];
            }, $tools)),
        ]);
    }
}
