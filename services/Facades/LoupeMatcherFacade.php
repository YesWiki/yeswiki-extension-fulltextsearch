<?php

namespace YesWiki\FullTextSearch\Services\Facades;

use Loupe\Matcher\Formatter;
use Loupe\Matcher\FormatterResult;

class LoupeMatcherFacade
{
    public const MARK_PREFIX = '<mark>';
    public const MARK_SUFFIX = '</mark>';

    private readonly Formatter $formatter;

    public function __construct(
    )
    {
        $tokenizer = new \Loupe\Matcher\Tokenizer\Tokenizer();
        $matcher = new \Loupe\Matcher\Matcher($tokenizer);

        $this->formatter = new \Loupe\Matcher\Formatter($matcher);
    }

    public function format(string $text, string $query): FormatterResult
    {
        $options = (new \Loupe\Matcher\FormatterOptions())
            ->withEnableHighlight()
            ->withHighlightStartTag(self::MARK_PREFIX)
            ->withHighlightEndTag(self::MARK_SUFFIX)
            ->withEnableCrop()
            ->withCropLength(40)
            ->withCropMarker('[...]')
        ;

        return $this->formatter->format($text, $query, $options);
    }
}
