<?php

namespace YesWiki\FullTextSearch\Services;

use CmsIg\Seal\Engine;
use CmsIg\Seal\Search\Condition;
use YesWiki\Core\Service\AclService;
use YesWiki\FullTextSearch\DTO\SearchEntryResponse;
use YesWiki\FullTextSearch\Services\Factory\SchemaFactory;
use YesWiki\FullTextSearch\Services\Factory\SearchEntryResponseFactory;
use YesWiki\FullTextSearch\Services\Repository\PageExclusionRepository;

class SealSearchService
{
    public const DEFAULT_LIMIT = 10;
    public const HIGHLIGHT_TAG_START = '<mark>';
    public const HIGHLIGHT_TAG_END = '</mark>';

    public function __construct(
        private readonly SearchEntryResponseFactory $searchEntryResponseFactory,
        private readonly AclService $aclService,
        private readonly PageExclusionRepository $pageExclusionRepository,
    ) {
    }

    /**
     * @return SearchEntryResponse[]
     */
    public function search(Engine $engine, string $query, int $limit): array
    {

        $limitPurified = $this->purifyLimit($limit);

        $currentOffset = 0;
        $res = [];
        while (true) {
            $request = $engine
                ->createSearchBuilder(SchemaFactory::INDEX_NAME)
                ->addFilter(new Condition\SearchCondition($query))
                ->highlight(['title', 'fulltext'], self::HIGHLIGHT_TAG_START, self::HIGHLIGHT_TAG_END)
                ->limit($limitPurified)
                ->offset($currentOffset)
            ;

            $exludedTags = $this->pageExclusionRepository->getAllExcludedTags();
            if (count($exludedTags) > 0) {
                $request->addFilter(new Condition\NotInCondition('tag_searchable', $exludedTags));
            }

            $engineRawResponse = iterator_to_array($request->getResult());
            if (count($engineRawResponse) === 0) {
                return $res;
            }
            $currentOffset += $limitPurified;
            foreach ($engineRawResponse as $entry) {
                if ($this->aclService->hasAccess('read', $entry['tag'])) {
                    $res[] = $this->searchEntryResponseFactory->create($entry);
                    if (count($res) >= $limitPurified) {
                        return $res;
                    }
                }
            }
        }
    }

    private function purifyLimit(int $limit): int
    {
        if ($limit <= 1) {
            return 1;
        }
        if ($limit > self::DEFAULT_LIMIT) {
            return self::DEFAULT_LIMIT;
        }

        return $limit;
    }
}
