<?php

namespace YesWiki\FullTextSearch\DTO;

class SearchEntryResponse extends SearchEntry
{
    public function __construct(
        string $tag,
        string $title,
        string $body,
        array $bazar,
        public readonly SearchEntryResponseExcerpt $excerpt
    ) {
        parent::__construct($tag, $title, $body, $bazar);
    }
}
