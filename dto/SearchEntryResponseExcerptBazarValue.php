<?php

namespace YesWiki\FullTextSearch\DTO;

use Loupe\Matcher\FormatterResult;

class SearchEntryResponseExcerptBazarValue
{
    public function __construct(
        public readonly string $id,
        public readonly string $label,
        public readonly FormatterResult $value
    ) {
    }
}
