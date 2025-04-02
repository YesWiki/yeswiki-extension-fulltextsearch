<?php

namespace YesWiki\Test\FullTextSearch\DTO;

require_once 'tools/fulltextsearch/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use YesWiki\FullTextSearch\DTO\SearchEntry;
use YesWiki\FullTextSearch\DTO\SearchEntryBazar;

class SearchEntryTest extends TestCase
{
    public function testNormalize()
    {
        $entry = new SearchEntry(
            'tag1',
            'title1',
            'body1',
            [
                new SearchEntryBazar('id1', 'value1'),
                new SearchEntryBazar('id2', 'value2'),
            ]
        );
        $expected = [
            'tag' => 'tag1',
            'tag_searchable' => 'tag1',
            'title' => 'title1',
            'body' => 'body1',
            'bazar' => [
                ['id' => 'id1', 'value' => 'value1'],
                ['id' => 'id2', 'value' => 'value2'],
            ],
        ];

        $this->assertEquals($expected, $entry->normalize());
    }
}
