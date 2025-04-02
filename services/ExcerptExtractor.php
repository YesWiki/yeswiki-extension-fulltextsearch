<?php

namespace YesWiki\FullTextSearch\Services;

/**
 * Seal engine does not support the `highlight` option on multi value field, so we need to implement our own excerpt extractor.
 * @ref https://github.com/PHP-CMSIG/search/issues/542
 */
class ExcerptExtractor
{
    public const MARK_PREFIX = '<mark>';
    public const MARK_SUFFIX = '</mark>';

    public const DEFAULT_MAX_LENGTH = 100;

    public function createExcerpt(string $text, string $word, int $maxLength = self::DEFAULT_MAX_LENGTH): string
    {
        $positions = [];
        $offset = 0;
        $wordLen = mb_strlen($word);
        $textLen = mb_strlen($text);

        while (($pos = mb_stripos($text, $word, $offset)) !== false) {
            $positions[] = $pos;
            $offset = $pos + $wordLen;
        }

        if (empty($positions)) {
            return '';
        }

        $excerpts = [];
        $ranges = [];

        foreach ($positions as $pos) {
            $start = max(0, $pos - intval($maxLength / 2));
            $end = min($textLen, $start + $maxLength);
            $ranges[] = [$start, $end];
        }

        usort($ranges, fn($a, $b) => $a[0] <=> $b[0]);
        $merged = [];
        foreach ($ranges as $range) {
            if (empty($merged) || $range[0] > $merged[count($merged)-1][1]) {
                $merged[] = $range;
            } else {
                $merged[count($merged)-1][1] = max($merged[count($merged)-1][1], $range[1]);
            }
        }

        foreach ($merged as [$start, $end]) {
            $excerpt = mb_substr($text, $start, $end - $start);
            $excerpt = preg_replace(
                '/' . preg_quote($word, '/') . '/iu',
                self::MARK_PREFIX . '$0' . self::MARK_SUFFIX,
                $excerpt
            );

            if ($start > 0) $excerpt = '...' . $excerpt;
            if ($end < $textLen) $excerpt .= '...';
            $excerpts[] = $excerpt;
        }

        return implode(' ', $excerpts);
    }
}
