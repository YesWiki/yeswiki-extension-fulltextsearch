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
            'placeholder' => $arg['placeholder'] ?? _t('WHAT_YOU_SEARCH'),
            'buttonside' => in_array($arg['buttonside'] ?? '', ['left', 'right']) ? $arg['buttonside'] : 'left',
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
            'tag' => $this->wiki->tag,
            'defaultQuery' => $this->wiki->request->query->get('fullTextSearch_search', ''),
            'placeholder' => $this->arguments['placeholder'],
            'buttonside' => $this->arguments['buttonside'],
        ]);
    }
}
