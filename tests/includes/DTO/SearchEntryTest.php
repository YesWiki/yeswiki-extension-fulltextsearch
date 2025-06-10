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
            'fulltext',
        );
        $expected = [
            'tag' => 'tag1',
            'tag_searchable' => 'tag1',
            'title' => 'title1',
            'fulltext' => 'fulltext',
        ];

        $this->assertEquals($expected, $entry->normalize());
    }
}
