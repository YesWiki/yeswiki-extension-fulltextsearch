<?php

namespace YesWiki\FullTextSearch\DTO;


class SearchEntryResponseExcerpt
{
    public function __construct(
        public readonly string $fulltext,
    )
    {
    }

    public function getFullTextCleaned(): string
    {
        return trim(preg_replace('/\s+/', ' ', $this->fulltext));
    }
}
