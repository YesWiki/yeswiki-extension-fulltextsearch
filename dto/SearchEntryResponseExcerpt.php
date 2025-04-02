<?php

namespace YesWiki\FullTextSearch\DTO;

use YesWiki\FullTextSearch\Services\ExcerptExtractor;

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
            if (str_contains($value->value, ExcerptExtractor::MARK_PREFIX)) {
                $markedFields[] = $value;
            }
        }

        return $markedFields;
    }
}
