<?php

namespace YesWiki\FullTextSearch\DTO;

class SearchEntry
{
    public function __construct(
        public readonly string $tag,
        public readonly string $title,
        public readonly string $body,
        /**
         * @var SearchEntryBazar[]
         */
        public readonly array $bazar
    ) {
    }

    public function normalize(): array
    {
        $res = [
            'tag' => $this->tag,
            'tag_searchable' => $this->tag,
            'title' => $this->title,
            'body' => $this->body,
            'bazar' => array_map(fn ($bazar) => ['id' => $bazar->id, 'value' => $bazar->value], $this->bazar),
        ];

        return $res;
    }
}
