<?php

namespace YesWiki\FullTextSearch\Services\Factory;

use CmsIg\Seal\Adapter\Loupe\LoupeAdapter;
use CmsIg\Seal\Adapter\Loupe\LoupeHelper;
use CmsIg\Seal\Adapter\Typesense\TypesenseAdapter;
use CmsIg\Seal\Engine;
use Loupe\Loupe\LoupeFactory;
use Symfony\Component\HttpClient\HttpClient;
use Typesense\Client;
use Http\Discovery\Psr17FactoryDiscovery;

class EngineLoupeFactory
{
    public function __construct(
        private readonly SchemaFactory $schemaFactory
    ) {
    }

    public function createEngineLoupe(): Engine
    {
        $loupeHelper = new LoupeHelper(
            new LoupeFactory(),
            getcwd() . '/private/search-indexes'
        );

        return new Engine(
            new LoupeAdapter($loupeHelper),
            $this->schemaFactory->createSchema(),
        );
    }
}
