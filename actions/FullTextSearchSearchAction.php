<?php

namespace YesWiki\FullTextSearch;

use YesWiki\Core\Service\TemplateEngine;
use YesWiki\Core\YesWikiAction;
use YesWiki\FullTextSearch\Services\SealFacade;
use YesWiki\FullTextSearch\Services\SealSearchService;

class FullTextSearchSearchAction extends YesWikiAction
{
    public function formatArguments($arg)
    {
        return [
            'limit' => $arg['limit'] ?? SealSearchService::LIMIT_DEFAULT,
        ];
    }

    public function run()
    {
        /** @var SealFacade $facade */
        $facade = $this->getService(SealFacade::class);

        if (!$facade->isEngineConfigured()) {
            return $this->render('@fulltextsearch/fulltextsearch-search-not-configured.html.twig');
        }

        return $this->render('@fulltextsearch/fulltextsearch-search.html.twig', [
            'engineConfigured' => $facade->isEngineConfigured(),
            'limit' => $this->arguments['limit'],
        ]);
    }
}
