<?php

namespace YesWiki\FullTextSearch\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use YesWiki\Core\ApiResponse;
use YesWiki\Core\YesWikiController;
use YesWiki\FullTextSearch\Services\Repository\PageRepository;
use YesWiki\FullTextSearch\Services\SealBatchImporter;
use YesWiki\FullTextSearch\Services\SealFacade;

class AdminInitController extends YesWikiController
{
    /**
     * @Route("/api/fulltextsearch/admin/init", methods={"POST"},options={"acl":{"@admins"}})
     */
    public function init()
    {
        $offset = (int) $this->wiki->request->request->get('offset', 0);
        if ($offset === 0) {
            $this->getService(SealFacade::class)->initEngine();
        }

        $total = $this->getService(PageRepository::class)->countPages();
        if ($offset >= $total) {
            return new Response(
                $this->render('@fulltextsearch/_fragments/button-init-success.html.twig')
            );
        }

        $nextOffset = $this->getService(SealBatchImporter::class)->batchImport($offset);

        return new Response(
            $this->render('@fulltextsearch/_fragments/button-init-processing.html.twig', [
                'offset' => $nextOffset,
                'progress' => round($offset / $total * 100, 2),
            ])
        );
    }

    /**
     * @Route("/api/fulltextsearch/admin/cleanup", methods={"POST"},options={"acl":{"@admins"}})
     */
    public function cleanup()
    {
        $this->getService(SealFacade::class)->cleanup();

        return new ApiResponse([], 201);
    }
}
