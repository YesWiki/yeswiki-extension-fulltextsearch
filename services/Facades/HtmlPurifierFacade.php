<?php

namespace YesWiki\FullTextSearch\Services\Facades;

/**
 * Wrapper around HTMLPurfier.
 * Use custom service to enforce rules.
 */
class HtmlPurifierFacade
{
    public const HTMLPURIFIER_CACHE_FOLDER = 'cache/HTMLpurifier_fulltextsearch';

    private readonly \HTMLPurifier $purifier;

    public function __construct()
    {
        $configNoHTML = \HTMLPurifier_Config::createDefault();
        $configNoHTML->set('HTML.Allowed', '');
        $configNoHTML->set('CSS.AllowedProperties', '');
        $configNoHTML->set('Core.RemoveProcessingInstructions', true);

        // set the cache folder
        // doc : http://htmlpurifier.org/live/configdoc/plain.html#Cache.SerializerPath
        if (!is_dir(self::HTMLPURIFIER_CACHE_FOLDER)) {
            mkdir(self::HTMLPURIFIER_CACHE_FOLDER, 0777, true);
        }
        $configNoHTML->set('Cache.SerializerPath', realpath(self::HTMLPURIFIER_CACHE_FOLDER));

        $this->purifier = new \HTMLPurifier($configNoHTML);
    }

    public function purify(string $input): string
    {
        return $this->purifier->purify($input);
    }
}
