<?php

namespace YesWiki\Test\FullTextSearch\Services;

require_once 'tools/fulltextsearch/vendor/autoload.php';

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use YesWiki\FullTextSearch\Services\Repository\PageRepository;
use YesWiki\FullTextSearch\Services\SealBatchImporter;
use YesWiki\FullTextSearch\Services\SealImporter;

class SealBatchImporterTest extends TestCase
{
    private readonly MockObject $pageManager;
    private readonly MockObject $sealImporter;

    public function setUp(): void
    {
        $this->pageManager = $this->createMock(PageRepository::class);
        $this->sealImporter = $this->createMock(SealImporter::class);
    }

    public function testImport()
    {
        $offset = 1;
        $pages = [
            ['id' => 1, 'title' => 'Page 1'],
            ['id' => 2, 'title' => 'Page 2'],
        ];

        $this->pageManager
            ->expects($this->once())
            ->method('getPages')
            ->with($offset, 123)
            ->willReturn($pages);

        $this->sealImporter
            ->expects($this->exactly(count($pages)))
            ->method('importPage')
            ->with($this->callback(function ($page) use (&$pages) {
                static $call = 0;

                return $page === $pages[$call++];
            }));

        $sealBatchImporter = new SealBatchImporter(
            $this->pageManager,
            $this->sealImporter,
            [
                'import_batch_size' => 123,
            ]
        );
        $newOffset = $sealBatchImporter->batchImport($offset);

        $this->assertEquals(124, $newOffset);
    }

    public function testSetDefaultBatchSize()
    {
        $offset = 1;

        $this->pageManager
            ->expects($this->once())
            ->method('getPages')
            ->with($offset, 1)
            ->willReturn([]);

        $sealBatchImporter = new SealBatchImporter(
            $this->pageManager,
            $this->sealImporter,
            [
            ]
        );
        $newOffset = $sealBatchImporter->batchImport($offset);

        $this->assertEquals(2, $newOffset);
    }
}
