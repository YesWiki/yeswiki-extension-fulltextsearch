<?php

namespace YesWiki\FullTextSearch\Services\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use YesWiki\Core\Entity\Event;
use YesWiki\FullTextSearch\Services\Repository\PageRepository;
use YesWiki\FullTextSearch\Services\SealImporter;

class UpdateIndexOnChange implements EventSubscriberInterface
{
    public function __construct(
        private readonly SealImporter   $sealImporter,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            'entry.created' => ['onUpdate'],
            'entry.updated' => ['onUpdate'],
            'page.created' => ['onUpdate'],
            'page.updated' => ['onUpdate'],
        ];
    }

    public function onUpdate(Event $event)
    {
        $id = $event->getData()['id'] ?? null;
        if (null === $id) {
            return;
        }

        $this->sealImporter->importPage(
            array_merge(
            ['id' => $id],
            $event->getData()['data'] ?? [],
        )
        );
    }
}
