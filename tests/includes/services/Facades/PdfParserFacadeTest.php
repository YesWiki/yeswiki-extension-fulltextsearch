<?php

namespace YesWiki\Test\FullTextSearch\Services\Facades;

require_once 'tools/fulltextsearch/vendor/autoload.php';

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Smalot\PdfParser\Parser;
use YesWiki\FullTextSearch\Services\Facades\PdfParserFacade;

class PdfParserFacadeTest extends TestCase
{
    private readonly PdfParserFacade $pdfParserFacade;
    private readonly MockObject $parser;

    public function setUp(): void
    {
        $this->parser = $this->createMock(Parser::class);
        $this->pdfParserFacade = new PdfParserFacade($this->parser);
    }

    public function testReturnNullOnException()
    {
        $this->parser->method('parseFile')->willThrowException(new \Exception('Test exception'));

        $result = $this->pdfParserFacade->parse('invalid/path/to/file.pdf');

        $this->assertNull($result);
    }

    public function testReturnParsedValue()
    {
        $this->parser->method('parseFile')->willReturn($this->mockPdfDocument('mocked content'));

        $result = $this->pdfParserFacade->parse('/path/to/file.pdf');

        $this->assertSame('mocked content', $result);
    }

    public function testFilterNonUTF8Character()
    {
        $this->parser->method('parseFile')->willReturn($this->mockPdfDocument("\xEB"));

        $result = $this->pdfParserFacade->parse('/path/to/file.pdf');

        $this->assertSame('?', $result);
    }

    private function mockPdfDocument(string $text): MockObject
    {
        $mock = $this->createMock(\Smalot\PdfParser\Document::class);
        $mock->method('getText')->willReturn($text);

        return $mock;
    }
}
