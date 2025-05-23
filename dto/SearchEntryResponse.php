<?php

namespace YesWiki\FullTextSearch\DTO;

class SearchEntryResponse extends SearchEntry
{
    public function __construct(
        string $tag,
        string $title,
        string $fulltext,
        public readonly SearchEntryResponseExcerpt $excerpt
    ) {
        parent::__construct($tag, $title, $fulltext);
    }
}
