<?php

namespace YesWiki\FullTextSearch\DTO;

use YesWiki\FullTextSearch\Services\ExcerptExtractor;
use YesWiki\FullTextSearch\Services\Facades\LoupeMatcherFacade;

class SearchEntryResponseExcerpt
{

    public function __construct(
        public readonly string $body,

    /**
     * @var SearchEntryResponseExcerptBazarValue[]
     */
    public readonly array $bazarValues
    ) {
    }

    /**
     * @return string[]
     */
    public function getBazarMarkedFields(): array
    {
        $markedFields = [];
        foreach ($this->bazarValues as $value) {
            if ($value->value->hasMatches()) {
                $markedFields[] = $value;
            }
        }

        return $markedFields;
    }
}
