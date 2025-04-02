<?php

namespace YesWiki\Test\FullTextSearch\DTO;

require_once 'tools/fulltextsearch/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use YesWiki\FullTextSearch\DTO\SearchEntryResponseExcerpt;
use YesWiki\FullTextSearch\DTO\SearchEntryResponseExcerptBazarValue;

class SearchEntryResponseFormattedTest extends TestCase
{
    public function testGetBazarMarkedFields()
    {
        $value1 = new SearchEntryResponseExcerptBazarValue('id1', 'label1', '<mark>value1</mark>');
        $value2 = new SearchEntryResponseExcerptBazarValue('id2', 'label2', 'value2');
        $entry = new SearchEntryResponseExcerpt(
            'body1',
            [
                $value1,
                $value2,
            ]
        );

        $this->assertEquals([$value1], $entry->getBazarMarkedFields());
    }
}
