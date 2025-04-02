<?php

namespace YesWiki\Test\FullTextSearch\Services;

require_once 'tools/fulltextsearch/vendor/autoload.php';


use PHPUnit\Framework\TestCase;

class ExcerptExtractorTest extends TestCase
{
    /**
     * @dataProvider excerptProvider
     */
    public function testCreateExcerpt(string $input, string $word, int $maxLength, string $expected)
    {
        $extractor = new \YesWiki\FullTextSearch\Services\ExcerptExtractor();

        $result = $extractor->createExcerpt($input, $word, $maxLength);

        // Check if the result contains the expected excerpt
        $this->assertSame($expected, $result);
    }

    public function excerptProvider(): array
    {
        return [
            'not found' => [
                'Cras varius eget purus eu vulputate. Nullam sem ipsum, tempor eget molestie eu, placerat a magna.',
                'invalid',
                20,
                ''
            ],
            'nominal case' => [
                'Cras varius eget purus eu vulputate. Nullam sem ipsum, tempor eget molestie eu, placerat a magna.',
                'Nullam',
                20,
                '...ulputate. <mark>Nullam</mark> sem...'
            ],
            'case insensitive' => [
                'Cras varius eget purus eu vulputate. Nullam sem ipsum, tempor eget molestie eu, placerat a magna.',
                'nullam',
                20,
                '...ulputate. <mark>Nullam</mark> sem...'
            ],
            'match first' => [
                'Cras varius eget purus eu vulputate. Nullam sem ipsum, tempor eget molestie eu, placerat a magna.',
                'Cras',
                20,
                '<mark>Cras</mark> varius eget pur...'
            ],
            'match last' => [
                'Cras varius eget purus eu vulputate. Nullam sem ipsum, tempor eget molestie eu, placerat a magna.',
                'magna.',
                20,
                '...lacerat a <mark>magna.</mark>'
            ],
            'multiple match' => [
                'Cras varius eget purus eu vulputate. Nullam sem ipsum, tempor eget molestie eu, placerat a magna.',
                'eu',
                20,
                '...get purus <mark>eu</mark> vulputa... ... molestie <mark>eu</mark>, placer...'
            ],
        ];
    }
}
