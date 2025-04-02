<?php

namespace YesWiki\FullTextSearch\Services\Repository;

use YesWiki\Core\Service\DbService;

class PageRepository
{
    public function __construct(
        private readonly DbService $dbService,
    ) {
    }

    public function getPages(int $offset, int $count): array
    {
        return $this->dbService->loadAll(
            sprintf(
                'SELECT * FROM %s WHERE latest = \'Y\' ORDER BY id ASC LIMIT %d OFFSET %d',
                $this->dbService->prefixTable('pages'),
                $count,
                $offset
            ),
        );
    }

    /**
     * @return string[]
     */
    public function getAllTags(): array
    {
        $rows = $this->dbService->loadAll(
            sprintf(
                'SELECT tag FROM %s WHERE latest = \'Y\' ORDER BY tag ASC',
                $this->dbService->prefixTable('pages'),
            ),
        );

        return array_column($rows, 'tag');
    }

    public function countPages(): int
    {
        $res = $this->dbService->loadSingle(
            sprintf(
                'SELECT COUNT(*) AS count FROM %s WHERE latest = \'Y\'',
                $this->dbService->prefixTable('pages'),
            ),
        );

        return (int)$res['count'];
    }
}
