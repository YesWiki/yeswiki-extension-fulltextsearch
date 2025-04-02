<?php

namespace YesWiki\FullTextSearch\Services;

use YesWiki\FullTextSearch\Services\Repository\PageRepository;

class SealBatchImporter
{
    public function __construct(
        private readonly PageRepository $pageManager,
        private readonly SealImporter   $sealImporter,
        private readonly array          $fullTextSearchConfig,
    ) {
    }

    public function batchImport(int $offset): int
    {
        $batchSize = $this->fullTextSearchConfig['import_batch_size'] ?? 1;
        $pages = $this->pageManager->getPages($offset, $batchSize);
        foreach ($pages as $page) {
            try {
                $this->sealImporter->importPage($page);
            } catch (\Exception $e) {
                // Log the error
                error_log('Error importing page: ' . $e->getMessage()); // TODO: change
            }
        }

        return $offset + $batchSize;
    }
}
