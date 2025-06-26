<?php

namespace YesWiki\FullTextSearch\DTO\Htmx;

class ToastMessageContainer
{
    public function __construct(
        /**
         * @var ToastMessage[]
         */
        public readonly array $messages = [],
    ) {
    }

    public function __toString(): string
    {
        return json_encode($this->normalize());
    }

    public function normalize(): array
    {
        return array_map(
            fn(ToastMessage $message) => $message->normalize(),
            $this->messages
        );
    }
}
