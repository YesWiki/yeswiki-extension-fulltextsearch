<?php

namespace YesWiki\FullTextSearch\Controller;

use Symfony\Component\Routing\Annotation\Route;
use YesWiki\Core\ApiResponse;
use YesWiki\Core\YesWikiController;
use YesWiki\FullTextSearch\Services\Repository\PageRepository;
use YesWiki\FullTextSearch\Services\SealBatchImporter;
use YesWiki\FullTextSearch\Services\SealFacade;

class AdminInitController extends YesWikiController
{
    /**
     * @Route("/api/fulltextsearch/admin/total", methods={"GET"},options={"acl":{"@admins"}})
     */
    public function total()
    {
        $total = $this->getService(PageRepository::class)->countPages();

        return new ApiResponse([
            'total' => $total,
        ]);
    }

    /**
     * @Route("/api/fulltextsearch/admin/init", methods={"POST"},options={"acl":{"@admins"}})
     */
    public function init()
    {
        $offset = (int)($this->wiki->request->toArray()['offset'] ?? 0);
        if ($offset === 0) {
            $this->getService(SealFacade::class)->initEngine();
        }

        $nextOffset = $this->getService(SealBatchImporter::class)->batchImport($offset);

        return new ApiResponse([
            'nextOffset' => $nextOffset,
        ]);
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
