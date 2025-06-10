<?php

namespace YesWiki\FullTextSearch\Services\Facades;

use Loupe\Matcher\Formatting\Cropper;
use YesWiki\FullTextSearch\Services\SealSearchService;

class LoupeMatcherFacade
{
    public const CROP_MARKER = '…';

    private function buildCropper(int $cropLength): Cropper
    {
        return new Cropper(
            cropLength: $cropLength,
            cropMarker: self::CROP_MARKER,
            highlightStartTag: SealSearchService::HIGHLIGHT_TAG_START,
            highlightEndTag: SealSearchService::HIGHLIGHT_TAG_END,
        );
    }

    public function crop(string $text, int $cropLength, int $maxLength): string
    {
        $cropped = $this->buildCropper($cropLength)->cropHighlightedText($text);
        $cropped = trim($cropped, $cropLength);
        return $this->removeLastCrop($cropped, $maxLength);
    }

    private function removeLastCrop(string $text, int $maxLength): string
    {
        $exploded = explode(self::CROP_MARKER, $text);
        $res = '';
        while(mb_strlen($res) < $maxLength && count($exploded) > 0) {
            $res .= array_shift($exploded) . self::CROP_MARKER;
        }

        return trim($res, self::CROP_MARKER);
    }
}
