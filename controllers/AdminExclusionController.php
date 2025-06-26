<?php

namespace YesWiki\FullTextSearch\Controller;

use Symfony\Component\HttpFoundation\Response;
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
        $tag = $this->wiki->request->request->get('tag', '');
        $pageExclusionRepo = $this->getService(PageExclusionRepository::class);
        if ($pageExclusionRepo->isExcluded($tag)) {
            $pageExclusionRepo->removeExclusion($tag);
        } else {
            $pageExclusionRepo->addExclusion($tag);
        }

        return new Response($this->render(
            '@fulltextsearch/_fragments/button-exclusion.html.twig',
            [
                'tag' => $tag,
                'exclusion' => $pageExclusionRepo->isExcluded($tag),
            ]
        ));
    }
}
