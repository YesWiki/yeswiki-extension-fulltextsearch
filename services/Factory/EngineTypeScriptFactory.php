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

class EngineTypeScriptFactory
{
    public function __construct(
        private readonly SchemaFactory $schemaFactory
    ) {
    }

    public function createEngineTypeScript(array $config): Engine
    {
        $client = new Client(
            [
                'api_key' => $config['api_key'] ?? '',
                'nodes' => [
                    [
                        'host' => $config['host'] ?? '',
                        'port' => $config['port'] ?? '',
                        'protocol' => $config['protocol'] ?? '',
                    ],
                ],
            ]
        );

        return new Engine(
            new TypesenseAdapter($client),
            $this->schemaFactory->createSchema(),
        );
    }
}
