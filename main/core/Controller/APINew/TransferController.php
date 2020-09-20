<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CoreBundle\Controller\APINew;

use Claroline\AppBundle\API\Options;
use Claroline\AppBundle\API\TransferProvider;
use Claroline\AppBundle\Controller\AbstractCrudController;
use Claroline\AppBundle\Event\StrictDispatcher;
use Claroline\CoreBundle\Entity\File\PublicFile;
use Claroline\CoreBundle\Entity\Import\File;
use Claroline\CoreBundle\Entity\Workspace\Workspace;
use Claroline\CoreBundle\Manager\ApiManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/transfer")
 */
class TransferController extends AbstractCrudController
{
    /** @var TransferProvider */
    private $provider;
    /** @var string */
    private $schemaDir;
    /** @var StrictDispatcher */
    private $dispatcher;
    /** @var ApiManager */
    private $apiManager;

    /**
     * TransferController constructor.
     *
     * @param TransferProvider $provider
     * @param string           $schemaDir
     * @param StrictDispatcher $dispatcher
     * @param ApiManager       $apiManager
     */
    public function __construct(
        TransferProvider $provider,
        $schemaDir,
        StrictDispatcher $dispatcher,
        ApiManager $apiManager
    ) {
        $this->provider = $provider;
        $this->schemaDir = $schemaDir;
        $this->dispatcher = $dispatcher;
        $this->apiManager = $apiManager;
    }

    public function getName()
    {
        return 'transfer';
    }

    public function getClass()
    {
        return File::class;
    }

    public function getIgnore()
    {
        return ['update', 'exist', 'schema'];
    }

    /**
     * @return array
     */
    public function getRequirements()
    {
        return [
            'get' => ['id' => '^(?!.*(schema|copy|parameters|find|transfer|\/)).*'],
            'update' => ['id' => '^(?!.*(schema|parameters|find|transfer|\/)).*'],
            'exist' => [],
        ];
    }

    /**
     * @Route("/upload/{workspaceId}", name="apiv2_transfer_upload_file", methods={"POST"})
     *
     * @param Request $request
     * @param int     $workspaceId
     *
     * @return JsonResponse
     */
    public function uploadFileAction(Request $request, $workspaceId = null)
    {
        $toUpload = $request->files->all()['file'];
        $handler = $request->get('handler');

        $object = $this->crud->create(PublicFile::class, [], ['file' => $toUpload]);

        $this->dispatcher->dispatch(strtolower('upload_file_'.$handler), 'File\UploadFile', [$object]);

        $file = $this->serializer->serialize($object);

        $workspace = null;
        if ($workspaceId) {
            $workspace = $this->om->getRepository(Workspace::class)->find($workspaceId);
        }

        $this->crud->create(File::class, [
            'uploadedFile' => $file,
            'workspace' => $workspace ? $this->serializer->serialize($workspace) : null,
        ]);

        return new JsonResponse([$file], 200);
    }

    /**
     * @Route("/workspace/{workspaceId}", name="apiv2_workspace_transfer_list", methods={"GET"})
     *
     * @param int     $workspaceId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function workspaceListAction($workspaceId, Request $request)
    {
        $query = $request->query->all();
        $options = $this->getOptions()['list'] ?? [];

        if (isset($query['options'])) {
            $options = $query['options'];
        }

        $query['hiddenFilters'] = ['workspace' => $workspaceId];

        return new JsonResponse($this->finder->search(
          self::getClass(),
          $query,
          $options
      ));
    }

    /**
     * @Route("/start", name="apiv2_transfer_start", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function startAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $file = $data['file'];
        unset($data['file']);
        $action = $data['action'];
        unset($data['action']);
        unset($data['format']); // posted by the form, but it's deduced from the file mime-type

        $publicFile = $this->om->getObject($file, PublicFile::class) ?? new PublicFile();
        $uuid = $request->get('workspace');
        $workspace = $this->om->getRepository(Workspace::class)->findOneBy(['uuid' => $uuid]);

        if ($workspace) {
            $data['workspace'] = $this->serializer->serialize($workspace, [Options::SERIALIZE_MINIMAL]);
        }

        $this->apiManager->import(
            $publicFile,
            $action,
            $request->query->get('log'),
            $data
        );

        return new JsonResponse(null, 204);
    }

    /**
     * @Route("/export/{format}", name="apiv2_transfer_export", methods={"GET"})
     *
     * @param Request $request
     * @param string  $format
     *
     * @return JsonResponse
     */
    public function exportAction(Request $request, $format)
    {
        $results = $this->finder->search(
            //maybe use a class map because it's the entity one currently
            $request->query->get('class'),
            $request->query->all(),
            []
        );

        return new JsonResponse(
            $this->provider->format($format, $results['data'], $request->query->all())
        );
    }

    /**
     * @Route("/action/{format}", name="apiv2_transfer_actions", methods={"GET"})
     *
     * @param string $format
     *
     * @return JsonResponse
     */
    public function getAvailableActionsAction($format)
    {
        return new JsonResponse(
            $this->provider->getAvailableActions($format)
        );
    }

    /**
     * @Route("/sample/{format}/{entity}/{name}/{sample}", name="apiv2_transfer_sample", methods={"GET"})
     *
     * @param string $name
     * @param string $entity
     * @param string $format
     * @param string $sample
     *
     * @return BinaryFileResponse
     */
    public function downloadSampleAction($name, $format, $entity, $sample)
    {
        $file = $this->provider->getSamplePath($format, $entity, $name, $sample);
        if (!$file) {
            throw new NotFoundHttpException('Sample file not found.');
        }

        return new BinaryFileResponse($file, 200, [
            'Content-Disposition' => "attachment; filename={$sample}",
        ]);
    }
}
