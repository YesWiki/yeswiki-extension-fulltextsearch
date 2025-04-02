<?php

namespace YesWiki\FullTextSearch\Services\Factory;

/**
 * Use a factory to avoid error in autoloading
 * Due to the way YesWiki manage plugin dependencies loading.
 */
class SmalotParserFactory
{
    public function create(): \Smalot\PdfParser\Parser
    {
        return new \Smalot\PdfParser\Parser();
    }
}
