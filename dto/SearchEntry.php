<?php

namespace YesWiki\FullTextSearch\DTO;

class SearchEntry
{
    public function __construct(
        public readonly string $tag,
        public readonly string $title,
        public readonly string $fulltext,
    ) {

    }

    public static function buildFromPageContent(
        string $tag,
        string $title,
        string $body,
        /**
         * @var SearchEntryBazar[]
         */
        array $bazar
    )
    {
        /**
         * SEAL does not support highlighting in multi value fields
         * So we need to concatenate all
         * This is not optimal for some search engines and need some processing
         * But it is the only way to do it
         *
         * @ref https://github.com/PHP-CMSIG/search/issues/542
         */
        $fulltext = implode(
            str_repeat(' ', 100),
            array_merge(
                [$body],
                array_map(
                    static fn (SearchEntryBazar $bazar) => $bazar->value,
                    $bazar
                )
            )
        );

        return new self(
            tag: $tag,
            title: $title,
            fulltext: trim($fulltext),
        );
    }

    public function normalize(): array
    {
        $res = [
            'tag' => $this->tag,
            'tag_searchable' => $this->tag,
            'title' => $this->title,
            'fulltext' => $this->fulltext,
        ];

        return $res;
    }
}
