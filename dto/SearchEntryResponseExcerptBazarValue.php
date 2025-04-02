<?php

namespace YesWiki\FullTextSearch\DTO;

class SearchEntryResponseExcerptBazarValue
{
    public function __construct(
        public readonly string $id,
        public readonly string $label,
        public readonly string $value
    ) {
    }
}
