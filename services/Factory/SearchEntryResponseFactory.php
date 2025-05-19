<?php

namespace YesWiki\FullTextSearch\Services\Factory;

use YesWiki\Bazar\Service\FormManager;
use YesWiki\FullTextSearch\DTO\SearchEntryBazar;
use YesWiki\FullTextSearch\DTO\SearchEntryResponse;
use YesWiki\FullTextSearch\DTO\SearchEntryResponseExcerpt;
use YesWiki\FullTextSearch\DTO\SearchEntryResponseExcerptBazarValue;
use YesWiki\FullTextSearch\Services\Facades\LoupeMatcherFacade;
use YesWiki\Templates\Service\Utils;

class SearchEntryResponseFactory
{
    public function __construct(
        private readonly Utils $utils,
        private readonly FormManager $formManager,
        private readonly LoupeMatcherFacade $loupeMatcherFacade,
    ) {
    }

    public function create(string $query, array $response): SearchEntryResponse
    {
        [
            'bazar' => $bazar,
            'formatted' => $excerpt,
        ] = $this->parseBazarAndExcerpt($query, $response['bazar'] ?? []);

        return new SearchEntryResponse(
            tag: $response['tag'],
            title: $this->utils->getTitleFromBody($response),
            body: $response['body'],
            bazar: $bazar,
            excerpt: new SearchEntryResponseExcerpt(
                $response['_formatted']['body'] ?? '',
                $excerpt
            ),
        );
    }

    private function parseBazarAndExcerpt(string $query, array $responseBazar)
    {
        $emptyResponse = [
            'bazar' => [],
            'formatted' => [],
        ];
        $formId = $this->getIdTypeAnnonce($responseBazar);
        if ($formId === null) {
            return $emptyResponse;
        }
        $form = $this->formManager->getOne($formId);
        if ($form === null) {
            return $emptyResponse;
        }
        $bazar = [];
        $excerpt = [];
        foreach ($responseBazar as $key => $value) {
            if (!isset($form['prepared'][$key])) {
                continue;
            }

            $bazar[] = new SearchEntryBazar($value['id'], $value['value']);
            $excerpt[] = new SearchEntryResponseExcerptBazarValue(
                id: $value['id'],
                label: $form['prepared'][$key]->getLabel() ?? '',
                value: $this->loupeMatcherFacade->format($value['value'], $query),
            );
        }

        return [
            'bazar' => $bazar,
            'formatted' => $excerpt,
        ];
    }

    private function getIdTypeAnnonce(array $bazarValues): ?string
    {
        foreach ($bazarValues as $value) {
            if ($value['id'] === 'id_typeannonce') {
                return $value['value'];
            }
        }

        return null;
    }
}
