<?php

namespace App\DTO;

class StateSubscriberConfigData extends BaseDTO
{
    protected array $subscribers = [];

    public function addSubscriber(string $subscriberClass, int $priority = 0): self
    {
        $this->subscribers[$subscriberClass] = $priority;

        return $this;
    }

    public function getSubscribers(): array
    {
        if (!$this->subscribers) {
            return $this->subscribers;
        }

        uasort(
            $this->subscribers,
            fn(int $priorityX, int $priorityY) => $priorityY <=> $priorityX
        );

        return array_keys($this->subscribers);
    }

    public function hasSubscriber(): bool
    {
        return !empty($this->subscribers);
    }
}
