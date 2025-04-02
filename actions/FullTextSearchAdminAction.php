<?php

namespace YesWiki\FullTextSearch;

use YesWiki\Core\YesWikiAction;
use YesWiki\FullTextSearch\Services\Repository\PageExclusionRepository;
use YesWiki\FullTextSearch\Services\Repository\PageRepository;
use YesWiki\FullTextSearch\Services\SealFacade;

class FullTextSearchAdminAction extends YesWikiAction
{
    public function run()
    {
        if (!empty($aclMessage = $this->checkSecuredACL())) {
            return $aclMessage;
        }

        /** @var SealFacade $facade */
        $facade = $this->getService(SealFacade::class);

        return $this->render('@fulltextsearch/fulltextsearch-admin.html.twig', [
            'engineConfigured' => $facade->isEngineConfigured(),
            'exclusions' => $this->generateExclusionMap(),
        ]);
    }

    /**
     * @return array<string, bool>
     */
    private function generateExclusionMap(): array
    {
        $tags = $this->getService(PageRepository::class)->getAllTags();
        $pageExclusionRepo = $this->getService(PageExclusionRepository::class);
        $exclusions = [];
        foreach ($tags as $tag) {
            $exclusions[$tag] = $pageExclusionRepo->isExcluded($tag);
        }

        return $exclusions;
    }
}
