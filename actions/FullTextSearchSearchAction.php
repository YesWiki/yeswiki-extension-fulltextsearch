<?php

namespace YesWiki\FullTextSearch;

use YesWiki\Core\YesWikiAction;
use YesWiki\FullTextSearch\Services\SealFacade;

class FullTextSearchSearchAction extends YesWikiAction
{
    public function run()
    {
        /** @var SealFacade $facade */
        $facade = $this->getService(SealFacade::class);

        if (!$facade->isEngineConfigured()) {
            return $this->render('@fulltextsearch/fulltextsearch-search-not-configured.html.twig');
        }

        return $this->render('@fulltextsearch/fulltextsearch-search.html.twig', [
            'engineConfigured' => $facade->isEngineConfigured(),
        ]);
    }
}
