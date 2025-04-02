<?php

namespace YesWiki\FullTextSearch\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use YesWiki\Core\YesWikiController;
use YesWiki\FullTextSearch\DTO\SearchEntryResponse;
use YesWiki\FullTextSearch\Services\Repository\BazarCategoryRepository;
use YesWiki\FullTextSearch\Services\SealFacade;

class SearchController extends YesWikiController
{
    /**
     * @Route("/api/fulltextsearch/search", methods={"POST"},options={"acl":{"public"}})
     */
    public function search()
    {
        $results = $this->getService(SealFacade::class)->search($this->wiki->request->toArray()['query'] ?? '');

        $categoryMap = $this->createTagCategryMap($results);

        return new Response($this->render('@fulltextsearch/fulltextsearch-search-result.html.twig', [
            'results' => $results,
            'categoryMap' => $categoryMap,
        ]));
    }

    /**
     * @param SearchEntryResponse[] $items
     * @return array<string, string>
     */
    private function createTagCategryMap(array $items): array
    {
        $bazarCategoryRepository = $this->getService(BazarCategoryRepository::class);
        $res = [];
        foreach ($items as $item) {
            if (!isset($res[$item->tag])) {
                $res[$item->tag] = $bazarCategoryRepository->getCategoryFromTag($item->tag);
            }
        }

        return $res;
    }
}
