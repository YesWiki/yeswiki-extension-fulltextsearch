<?php

namespace YesWiki\FullTextSearch\DTO\Htmx;

class ToastMessage
{
    public function __construct(
        public readonly string $message,
        public readonly ?int $duration = null,
        public readonly ?string $toastClass = null,
    ) {
    }

    public function normalize(): array
    {
        return [
            'message' => $this->message,
            'duration' => $this->duration,
            'toastClass' => $this->toastClass,
        ];
    }
}
