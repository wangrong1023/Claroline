<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\HomeBundle\DataFixtures\PostInstall;

use Claroline\AppBundle\API\SerializerProvider;
use Claroline\HomeBundle\Entity\HomeTab;
use Claroline\HomeBundle\Entity\Type\WidgetsTab;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Create a default tab in desktop Home and
 * add a widget to display the list of workspaces of the current user.
 */
class LoadDesktopHomeData extends AbstractFixture implements ContainerAwareInterface
{
    /** @var TranslatorInterface */
    private $translator;
    /** @var SerializerProvider */
    private $serializer;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->translator = $container->get('translator');
        $this->serializer = $container->get(SerializerProvider::class);
    }

    public function load(ObjectManager $manager)
    {
        $existingTabs = $manager->getRepository(HomeTab::class)->findBy([
            'context' => HomeTab::TYPE_ADMIN_DESKTOP,
        ]);

        if (empty($existingTabs)) {
            $defaultTab = [
                'title' => $this->translator->trans('informations', [], 'platform'),
                'longTitle' => $this->translator->trans('informations', [], 'platform'),
                'slug' => 'informations',
                'context' => HomeTab::TYPE_ADMIN_DESKTOP,
                'type' => WidgetsTab::getType(),
                'class' => WidgetsTab::class,
                'position' => 1,
                'restrictions' => [
                    'hidden' => false,
                ],
                'parameters' => [
                    'widgets' => [[
                        'name' => $this->translator->trans('my_workspaces', [], 'workspace'),
                        'visible' => true,
                        'display' => [
                            'layout' => [1],
                            'color' => '#333333',
                            'backgroundType' => 'color',
                            'background' => '#ffffff',
                        ],
                        'parameters' => [],
                        'contents' => [[
                            'type' => 'list',
                            'source' => 'my_workspaces',
                            'parameters' => [
                                'display' => 'tiles-sm',
                                'enableDisplays' => false,
                                'availableDisplays' => [],
                                'card' => [
                                    'display' => [
                                        'icon',
                                        'flags',
                                        'subtitle',
                                        'description',
                                        'footer',
                                    ],
                                ],
                                'paginated' => true,
                                'count' => true,
                            ],
                        ]],
                    ]],
                ],
            ];

            $tab = $this->serializer->deserialize($defaultTab, new HomeTab());

            $manager->persist($tab);
            $manager->flush();
        }
    }
}
