<?php

namespace YesWiki\FullTextSearch\DTO;

class SearchEntryBazar
{
    public function __construct(
        public readonly string $id,
        public readonly string $value
    ) {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
