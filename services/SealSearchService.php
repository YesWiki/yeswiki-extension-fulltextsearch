<?php

namespace YesWiki\FullTextSearch\Services;

use CmsIg\Seal\Engine;
use CmsIg\Seal\Search\Condition;
use YesWiki\Core\Service\AclService;
use YesWiki\FullTextSearch\DTO\SearchEntryResponse;
use YesWiki\FullTextSearch\DTO\SearchEntryResponseExcerpt;
use YesWiki\FullTextSearch\Services\Factory\EngineFactory;
use YesWiki\FullTextSearch\Services\Factory\SchemaFactory;
use YesWiki\FullTextSearch\Services\Factory\SearchEntryResponseFactory;
use YesWiki\FullTextSearch\Services\Repository\PageExclusionRepository;

class SealSearchService
{
    public const LIMIT = 10;

    public function __construct(
        private readonly SearchEntryResponseFactory $searchEntryResponseFactory,
        private readonly AclService $aclService,
        private readonly PageExclusionRepository $pageExclusionRepository,
    ) {
    }

    /**
     * @return SearchEntryResponse[]
     */
    public function search(Engine $engine, string $query): array
    {
        $currentOffset = 0;
        $res = [];
        while (true) {
            $request = $engine
                ->createSearchBuilder(SchemaFactory::INDEX_NAME)
                ->addFilter(new Condition\SearchCondition($query))
                ->highlight(['body', 'fulltext'])
                ->limit(self::LIMIT)
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
            $currentOffset += self::LIMIT;
            foreach ($engineRawResponse as $entry) {
                if ($this->aclService->hasAccess('read', $entry['tag'])) {
                    $res[] = $this->searchEntryResponseFactory->create($entry);
                    if (count($res) >= self::LIMIT) {
                        return $res;
                    }
                }
            }
        }
    }
}
