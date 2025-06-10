<?php

namespace YesWiki\Test\FullTextSearch\Services\Facades;

require_once 'tools/fulltextsearch/vendor/autoload.php';

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Smalot\PdfParser\Parser;
use YesWiki\FullTextSearch\Services\Facades\LoupeMatcherFacade;
use YesWiki\FullTextSearch\Services\Facades\PdfParserFacade;

class LoupeMatcherFacadeTest extends TestCase
{
    public const TEST_CROP_LENGTH = 20;
    public const TEST_MAX_LENGTH = 100;

    private readonly LoupeMatcherFacade $facade;

    public function setUp(): void
    {
        $this->facade = new LoupeMatcherFacade();
    }

    /**
     * @dataProvider cropProvider
     */
    public function testCrop(string $input, string $expected): void
    {
        $res = $this->facade->crop($input, self::TEST_CROP_LENGTH, self::TEST_MAX_LENGTH);

        $this->assertEquals($expected, $res);
    }

    public function cropProvider(): array
    {
        return [
            ['', ''],
            ['small', 'small'],
            [
                '<mark>Lorem ipsum dolor sit amet,</mark>',
                '<mark>Lorem ipsum dolor sit amet,</mark>'
            ],
            [
                '<mark>Lorem ipsum dolor sit amet,</mark>fjisd fdsjmfds fjkslfds<mark>Lorem ipsum dolor sit amet,</mark>',
                '<mark>Lorem ipsum dolor sit amet,</mark>…<mark>Lorem ipsum dolor sit amet,</mark>'
            ],
            [
                '<mark>Lorem</mark>fjisd fdsjmfds fjkslfds<mark>Lorem2</mark>',
                '<mark>Lorem</mark>fjisd fdsjmfds…fjkslfds<mark>Lorem2</mark>'
            ],
            [
                '<mark>Lorem</mark>fjisd fdsjmfds fjkslfds<mark>Lorem2</mark>fjisd fdsjmfds fjkslfds<mark>Lorem2</mark>fjisd fdsjmfds fjkslfds<mark>Lorem2</mark>',
                '<mark>Lorem</mark>fjisd fdsjmfds…fjkslfds<mark>Lorem2</mark>fjisd fdsjmfds…fjkslfds<mark>Lorem2</mark>fjisd fdsjmfds'
            ],
        ];
    }
}
