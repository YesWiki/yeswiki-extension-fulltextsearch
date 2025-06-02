<?php

namespace YesWiki\FullTextSearch\Services\Factory;

use YesWiki\Bazar\Field\BazarField;
use YesWiki\Bazar\Field\FileField;
use YesWiki\Bazar\Service\EntryManager;
use YesWiki\Bazar\Service\FormManager;
use YesWiki\Core\Service\Performer;
use YesWiki\FullTextSearch\DTO\SearchEntry;
use YesWiki\FullTextSearch\DTO\SearchEntryBazar;
use YesWiki\FullTextSearch\Services\Facades\HtmlPurifierFacade;
use YesWiki\FullTextSearch\Services\Facades\PdfParserFacade;
use YesWiki\Templates\Service\Utils;

class SearchEntryFactory
{
    public function __construct(
        private readonly EntryManager $entryManager,
        private readonly Utils $utils,
        private readonly Performer $performer,
        private readonly FormManager $formManager,
        private readonly PdfParserFacade $pdfParserFacade,
        private readonly HtmlPurifierFacade $htmlPurifierFacade,
    ) {
    }

    public function createFromPage(array $page): SearchEntry
    {
        return SearchEntry::buildFromPageContent(
            tag: $page['tag'],
            title: $this->htmlPurifierFacade->purify($this->utils->getTitleFromBody($page)),
            body: $this->createContent($page),
            bazar: $this->createBazar($page),
        );
    }

    private function createContent(array $page): string
    {
        if ($this->entryManager->isEntry($page['tag'])) {
            return '';
        }

        $pageBody = $page['body'] ?? '';
        $pageBody = preg_replace('{{.*}}', '', $pageBody); // Remove all tags

        return $this->htmlPurifierFacade->purify(
            $this->performer->run('wakka', 'formatter', ['text' => $pageBody])
        );
    }

    /**
     * @return SearchEntryBazar[]
     */
    private function createBazar(array $page): array
    {
        if (!$this->entryManager->isEntry($page['tag'])) {
            return [new SearchEntryBazar('', '')];
        }

        $entry = $this->entryManager->getOne($page['tag']);
        $form = $this->formManager->getOne($entry['id_typeannonce']);
        if($form === null) {
            return [new SearchEntryBazar('', '')];
        }

        $bazar = [];
        foreach ($entry as $key => $value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }
            $field = $this->getPreparedFieldFromForm($form, $key);
            if ($field instanceof FileField && $value !== '') {
                $value = $this->pdfParserFacade->parse('files/' . $value);
            }

            if (!is_string($value)) {
                $value = (string)$value;
            }
            $bazar[] = new SearchEntryBazar(
                $key,
                $this->htmlPurifierFacade->purify($value)
            );
        }

        return $bazar;
    }

    private function getPreparedFieldFromForm(array $form, string $key): ?BazarField
    {
        foreach ($form['prepared'] as $field) {
            if ($field->getPropertyName() === $key) {
                return $field;
            }
        }

        return null;
    }
}
