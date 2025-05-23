<?php

namespace YesWiki\FullTextSearch\Services\Factory;

use YesWiki\FullTextSearch\DTO\SearchEntryResponse;
use YesWiki\FullTextSearch\DTO\SearchEntryResponseExcerpt;
use YesWiki\FullTextSearch\Services\Facades\LoupeMatcherFacade;

class SearchEntryResponseFactory
{
    public function __construct(
        private readonly LoupeMatcherFacade $loupeMatcherFacade
    )
    {
    }

    public function create(array $response): SearchEntryResponse
    {
        return new SearchEntryResponse(
            tag: $response['tag'],
            title: $response['title'],
            fulltext: $response['fulltext'],
            excerpt: new SearchEntryResponseExcerpt(
                title: $response['_formatted']['title'] ?? '',
                fulltext: $this->loupeMatcherFacade->crop($response['_formatted']['fulltext'] ?? '')
            )
        );
    }
}
