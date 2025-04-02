<?php

namespace YesWiki\FullTextSearch\Services\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use YesWiki\Core\Entity\Event;
use YesWiki\FullTextSearch\Services\SealFacade;

class DeleteIndexOnDelete implements EventSubscriberInterface
{
    public function __construct(
        private readonly SealFacade $sealFacade,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            'entry.deleted' => ['onDeleted'],
            'page.deleted' => ['onDeleted'],
        ];
    }

    public function onDeleted(Event $event)
    {
        $id = $event->getData()['id'] ?? null;
        if (null === $id) {
            return;
        }

        $this->sealFacade->delete($id);
    }
}
