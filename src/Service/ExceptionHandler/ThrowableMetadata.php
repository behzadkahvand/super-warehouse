<?php

namespace App\Service\ExceptionHandler;

final class ThrowableMetadata
{
    public function __construct(
        private bool $isVisibleForUsers,
        private int $statusCode,
        private string $title
    ) {
    }

    public function isVisibleForUsers(): bool
    {
        return $this->isVisibleForUsers;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
