<?php

namespace YesWiki\FullTextSearch\Services\Repository;

use YesWiki\Bazar\Service\EntryManager;
use YesWiki\Bazar\Service\FormManager;

class BazarCategoryRepository
{
    public function __construct(
        private readonly EntryManager $entryManager,
        private readonly FormManager $formManager
    ) {
    }

    public function getCategoryFromTag(string $tag): string
    {
        $entry = $this->entryManager->getOne($tag);
        if ($entry === null) {
            return _t('FULLTEXTSEARCH_RESULT_CATEGORY_NONE');
        }

        $form = $this->formManager->getOne($entry['id_typeannonce']);

        $category = $form['bn_label_nature'] ?? null;
        if($category === null) {
            return _t('FULLTEXTSEARCH_RESULT_CATEGORY_NONE');
        }

        return $category;
    }
}
