<?php

namespace YesWiki\FullTextSearch\Services;

use CmsIg\Seal\Engine;
use YesWiki\FullTextSearch\DTO\SearchEntry;
use YesWiki\FullTextSearch\DTO\SearchEntryResponse;
use YesWiki\FullTextSearch\Services\Factory\EngineFactory;
use YesWiki\FullTextSearch\Services\Factory\SchemaFactory;

/**
 * Facade for the Seal engine.
 * Use only this service for all operations on the Seal engine.
 */
class SealFacade
{
    private readonly Engine $engine;

    public function __construct(
        private readonly EngineFactory $engineFactory,
        private readonly SealSearchService $sealSearchService,
    ) {
    }

    /**
     * Use singleton pattern to place engine initialization on the later possible stage.
     * This ensure most of the yeswiki error management is initialized.
     */
    public function getEngine(): Engine
    {
        if (!isset($this->engine)) {
            $this->engine = $this->engineFactory->create();
        }

        return $this->engine;
    }

    public function isEngineConfigured(): bool
    {
        return $this->getEngine()->existIndex(SchemaFactory::INDEX_NAME);
    }

    public function cleanup(): void
    {
        if ($this->isEngineConfigured()) {
            $this->getEngine()->dropIndex(SchemaFactory::INDEX_NAME);
        }
    }

    public function initEngine(): void
    {
        $this->cleanup();

        $this->getEngine()->createIndex(SchemaFactory::INDEX_NAME);
    }

    public function delete(string $tag): void
    {
        $this->getEngine()->deleteDocument(SchemaFactory::INDEX_NAME, $tag);
    }

    /**
     * @return SearchEntryResponse[]
     */
    public function search(string $query, int $limit): array
    {
        return $this->sealSearchService->search($this->getEngine(), $query, $limit);
    }

    public function saveDocument(SearchEntry $searchEntry)
    {
        $this->getEngine()->saveDocument(SchemaFactory::INDEX_NAME, $searchEntry->normalize());
    }
}
