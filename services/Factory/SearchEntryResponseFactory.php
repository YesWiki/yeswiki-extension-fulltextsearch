<?php

namespace YesWiki\FullTextSearch\Services\Factory;

use YesWiki\FullTextSearch\DTO\SearchEntryResponse;
use YesWiki\FullTextSearch\DTO\SearchEntryResponseExcerpt;

class SearchEntryResponseFactory
{
    public function create(array $response): SearchEntryResponse
    {
        return new SearchEntryResponse(
            tag: $response['tag'],
            title: $response['title'],
            fulltext: $response['fulltext'],
            excerpt: new SearchEntryResponseExcerpt(
                title: $response['_formatted']['title'] ?? '',
                fulltext: $response['_formatted']['fulltext'] ?? ''
            )
        );
    }
}
