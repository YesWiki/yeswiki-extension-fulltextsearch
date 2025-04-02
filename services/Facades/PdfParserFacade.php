<?php

namespace YesWiki\FullTextSearch\Services\Facades;

use Smalot\PdfParser\Parser;

class PdfParserFacade
{
    public function __construct(
        private readonly Parser $parser
    ) {
    }

    public function parse(string $filePath): ?string
    {
        try {
            $pdf = $this->parser->parseFile($filePath);
        } catch (\Exception $e) {
            return null;
        }

        $res = $pdf->getText();

        $res = mb_convert_encoding($res, 'UTF-8', 'UTF-8'); // Remove non UTF8 characters

        return $res;
    }
}
