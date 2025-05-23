<?php

namespace YesWiki\FullTextSearch\Services\Facades;

use Loupe\Matcher\Formatting\Cropper;
use YesWiki\FullTextSearch\Services\SealSearchService;

class LoupeMatcherFacade
{
    public const CROP_LENGTH = 200;
    public const CROP_MARKER = '…';
    private Cropper $cropper;

    public function __construct(
    ) {
        $this->cropper = new \Loupe\Matcher\Formatting\Cropper(
            cropLength: self::CROP_LENGTH,
            cropMarker: self::CROP_MARKER,
            highlightStartTag: SealSearchService::HIGHLIGHT_TAG_START,
            highlightEndTag: SealSearchService::HIGHLIGHT_TAG_END,
        );
    }

    public function crop(string $text): string
    {
        $cropped = $this->cropper->cropHighlightedText($text);
        return trim($cropped, self::CROP_MARKER);
    }
}
