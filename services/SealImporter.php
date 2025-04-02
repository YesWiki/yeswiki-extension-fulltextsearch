<?php

namespace YesWiki\FullTextSearch\Services;

use YesWiki\FullTextSearch\Services\Factory\SearchEntryFactory;

class SealImporter
{
    public function __construct(
        private readonly SearchEntryFactory $searchEntryFactory,
        private readonly SealFacade $sealFacade,
    ) {
    }

    public function importPage(array $page): void
    {
        if (!$this->sealFacade->isEngineConfigured()) {
            return;
        }

        if ($this->isExcludedSystemPage($page)) {
            return;
        }

        $this->sealFacade->saveDocument($this->searchEntryFactory->createFromPage($page));
    }

    private function isExcludedSystemPage(array $page): bool
    {
        $tags = $page['tag'] ?? '';
        if (str_starts_with($tags, 'LogDesActionsAdministratives')) {
            return true;
        }

        return false;
    }
}
