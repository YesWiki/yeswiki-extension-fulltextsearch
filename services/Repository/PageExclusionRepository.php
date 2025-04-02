<?php

namespace YesWiki\FullTextSearch\Services\Repository;

use YesWiki\Core\Service\TripleStore;

class PageExclusionRepository
{
    public const STORE_RESOURCE = 'FullTextSearch:exclusion';
    public const STORE_PROPERTY = 'http://outils-reseaux.org/_vocabulary/metadata';

    public function __construct(
        private readonly TripleStore $tripleStore,
    ) {
    }

    public function isExcluded(string $tag): bool
    {
        // use getAll function instead of exist to trigger the internal cache.
        return in_array($tag, $this->getAllExcludedTags(), true);
    }

    /**
     * @return string[]
     */
    public function getAllExcludedTags(): array
    {
        $storeTags = $this->tripleStore->getAll(self::STORE_RESOURCE, self::STORE_PROPERTY);

        return array_map(
            static fn ($tag) => $tag['value'],
            $storeTags
        );
    }

    public function addExclusion(string $tag): void
    {
        $this->tripleStore->create(self::STORE_RESOURCE, self::STORE_PROPERTY, $tag);
    }

    public function removeExclusion(string $tag): void
    {
        $this->tripleStore->delete(self::STORE_RESOURCE, self::STORE_PROPERTY, $tag);
    }
}
