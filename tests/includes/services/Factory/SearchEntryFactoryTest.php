<?php

namespace YesWiki\Test\FullTextSearch\Services\Factory;

require_once 'tools/fulltextsearch/vendor/autoload.php';
require_once 'includes/autoload.inc.php';

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use YesWiki\Bazar\Field\EmailField;
use YesWiki\Bazar\Field\TextField;
use YesWiki\Bazar\Service\EntryManager;
use YesWiki\Bazar\Service\FormManager;
use YesWiki\Core\Service\Performer;
use YesWiki\FullTextSearch\Services\Facades\HtmlPurifierFacade;
use YesWiki\FullTextSearch\Services\Facades\PdfParserFacade;
use YesWiki\FullTextSearch\Services\Factory\SearchEntryFactory;
use YesWiki\Templates\Service\Utils;

class SearchEntryFactoryTest extends TestCase
{
    private const EMAIL = 'jane.doe@example.org';

    public function testEmailReplacedByContactButtonIsNotIndexed()
    {
        $searchEntry = $this->createFactory('form')->createFromPage($this->getPage());

        $this->assertStringNotContainsString(self::EMAIL, $searchEntry->fulltext);
        // other fields are still indexed
        $this->assertStringContainsString('Portrait de Jane', $searchEntry->fulltext);
        // values without a form field are still indexed
        $this->assertStringContainsString('2026-09-15 10:00:00', $searchEntry->fulltext);
    }

    public function testEmailDisplayedAsTextIsIndexed()
    {
        $searchEntry = $this->createFactory('')->createFromPage($this->getPage());

        $this->assertStringContainsString(self::EMAIL, $searchEntry->fulltext);
    }

    private function createFactory(string $replaceEmailByButton): SearchEntryFactory
    {
        $services = $this->createStub(ContainerInterface::class);

        $entryManager = $this->createStub(EntryManager::class);
        $entryManager->method('isEntry')->willReturn(true);
        $entryManager->method('getOne')->willReturn([
            'id_fiche' => 'JaneDoe',
            'id_typeannonce' => '1',
            'bf_titre' => 'Portrait de Jane',
            'bf_mail' => self::EMAIL,
            'date_creation_fiche' => '2026-09-15 10:00:00',
            'date_maj_fiche' => '2026-09-15 10:00:00',
            'statut_fiche' => '1',
            'owner' => 'WikiAdmin',
            'user' => 'WikiAdmin',
            // added by EntryManager for rendering, duplicates field values
            'html_data' => 'data-bf_mail="' . self::EMAIL . '" data-id_typeannonce="1" data-id_fiche="JaneDoe" '
                . 'data-date_creation_fiche="2026-09-15 10:00:00" data-statut_fiche="1" '
                . 'data-date_maj_fiche="2026-09-15 10:00:00" data-owner="WikiAdmin" ',
            'url' => 'http://localhost/?JaneDoe',
        ]);

        $formManager = $this->createStub(FormManager::class);
        $formManager->method('getOne')->willReturn([
            'bn_id_nature' => '1',
            'bn_label_nature' => 'Annuaire',
            'prepared' => [
                new TextField($this->getFieldValues(['texte', 'bf_titre']), $services),
                new EmailField($this->getFieldValues([0 => 'champs_mail', 1 => 'bf_mail', 6 => $replaceEmailByButton]), $services),
            ],
        ]);

        $utils = $this->createStub(Utils::class);
        $utils->method('getTitleFromBody')->willReturn('Portrait de Jane');

        return new SearchEntryFactory(
            $entryManager,
            $utils,
            $this->createStub(Performer::class),
            $formManager,
            $this->createStub(PdfParserFacade::class),
            new HtmlPurifierFacade(),
            ['entries_pdf_indexing' => true],
        );
    }

    private function getFieldValues(array $values): array
    {
        return array_replace(array_fill(0, 16, ''), $values);
    }

    private function getPage(): array
    {
        return [
            'id' => '42',
            'tag' => 'JaneDoe',
            'time' => '2026-09-15 10:00:00',
            'body' => '{"bf_titre":"Portrait de Jane","bf_mail":"' . self::EMAIL . '"}',
            'owner' => 'WikiAdmin',
            'user' => 'WikiAdmin',
            'latest' => 'Y',
            'comment_on' => '',
        ];
    }
}
