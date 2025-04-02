<?php

namespace YesWiki\FullTextSearch\Controller;

use Symfony\Component\Routing\Annotation\Route;
use YesWiki\Core\ApiResponse;
use YesWiki\Core\YesWikiController;
use YesWiki\FullTextSearch\Services\Repository\PageExclusionRepository;

class AdminExclusionController extends YesWikiController
{
    /**
     * @Route("/api/fulltextsearch/admin/exclusions/toggle", methods={"POST"},options={"acl":{"@admins"}})
     */
    public function toggle()
    {
        $tag = $this->wiki->request->toArray()['tag'] ?? '';
        $pageExclusionRepo = $this->getService(PageExclusionRepository::class);
        if ($pageExclusionRepo->isExcluded($tag)) {
            $pageExclusionRepo->removeExclusion($tag);
        } else {
            $pageExclusionRepo->addExclusion($tag);
        }

        return new ApiResponse([
            'newState' => $pageExclusionRepo->isExcluded($tag),
        ]);
    }
}
