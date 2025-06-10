<?php

namespace YesWiki\FullTextSearch\Services\Factory;

use YesWiki\FullTextSearch\DTO\SearchEntryResponse;
use YesWiki\FullTextSearch\DTO\SearchEntryResponseExcerpt;
use YesWiki\FullTextSearch\Services\Facades\LoupeMatcherFacade;

class SearchEntryResponseFactory
{
    private const DEFAULT_CROP_LENGTH = 50;
    private const DEFAULT_RESULT_MAX_LENGTH = 200;

    public function __construct(
        private readonly LoupeMatcherFacade $loupeMatcherFacade,
        private readonly array          $fullTextSearchConfig
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
                fulltext: $this->loupeMatcherFacade->crop(
                    $response['_formatted']['fulltext'] ?? ''
                    ,
                        (int) ($this->fullTextSearchConfig['rendering']['length_crop'] ?? self::DEFAULT_CROP_LENGTH),
                        (int) ($this->fullTextSearchConfig['rendering']['length_excerpt_max'] ?? self::DEFAULT_RESULT_MAX_LENGTH),
                )
            )
        );
    }
}
