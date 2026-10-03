<?php

namespace YesWiki\Test\FullTextSearch\Services;

require_once 'tools/fulltextsearch/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use YesWiki\Core\Entity\Event;
use YesWiki\FullTextSearch\Services\EventSubscriber\UpdateIndexOnChange;
use YesWiki\FullTextSearch\Services\SealImporter;

class UpdateIndexOnChangeTest extends TestCase
{
    public function testAnEntryEventIsImportedUnderItsTag()
    {
        $sealImporter = $this->createMock(SealImporter::class);
        $sealImporter->expects($this->once())
            ->method('importPage')
            ->with($this->callback(fn (array $page) => ($page['tag'] ?? null) === 'MyEntry'));

        (new UpdateIndexOnChange($sealImporter))->onUpdate(new Event([
            'id' => 'MyEntry',
            'data' => ['id_fiche' => 'MyEntry', 'bf_titre' => 'My entry'],
        ]));
    }

    public function testAPageEventKeepsTheTagItCarries()
    {
        $sealImporter = $this->createMock(SealImporter::class);
        $sealImporter->expects($this->once())
            ->method('importPage')
            ->with($this->callback(fn (array $page) => $page['tag'] === 'MyPage' && $page['body'] === 'text'));

        (new UpdateIndexOnChange($sealImporter))->onUpdate(new Event([
            'id' => 42,
            'data' => ['tag' => 'MyPage', 'body' => 'text'],
        ]));
    }
}
