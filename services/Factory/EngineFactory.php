<?php

namespace YesWiki\FullTextSearch\Services\Factory;

use CmsIg\Seal\Engine;
use YesWiki\FullTextSearch\Enum\EngineDriver;
use YesWiki\FullTextSearch\Exception\EngineDriverNotFound;

class EngineFactory
{
    public function __construct(
        private readonly EngineLoupeFactory $engineLoupeFactory,
        private readonly EngineTypeScriptFactory $engineTypeScriptFactory,
        private readonly array          $fullTextSearchConfig,
    ) {
    }

    public function create(): Engine
    {
        $engineConfig = $this->fullTextSearchConfig['engine_config'] ?? [];
        $engineDriver = EngineDriver::tryFrom($engineConfig['driver'] ?? '');
        if($engineDriver === null) {
            throw new EngineDriverNotFound();
        }
        return match ($engineDriver) {
            EngineDriver::LOUPE => $this->engineLoupeFactory->createEngineLoupe(),
            EngineDriver::TYPESENSE => $this->engineTypeScriptFactory->createEngineTypeScript($engineConfig['typesense_config'] ?? []),
            default => throw new EngineDriverNotFound(),
        };
    }
}
