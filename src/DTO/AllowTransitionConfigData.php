<?php

namespace App\DTO;

class AllowTransitionConfigData extends BaseDTO
{
    protected ?string $defaultState = null;

    protected array $allowedTransitions = [];

    public function setDefault(string $default): self
    {
        $this->defaultState = $default;

        return $this;
    }

    public function getDefault(): ?string
    {
        return $this->defaultState;
    }

    public function addAllowTransition(string $from, string $to): self
    {
        $this->addAllowTransitions($from, [$to]);

        return $this;
    }

    public function addAllowTransitions(string $from, array $to): self
    {
        $toTransitions = $this->getTransitions($from);

        $this->allowedTransitions[$from] = array_merge($toTransitions, $to);

        return $this;
    }

    public function getAllowedTransitions(): array
    {
        return $this->allowedTransitions;
    }

    public function getAllowedTransitionsFor(string $from): array
    {
        return $this->getTransitions($from);
    }

    public function isTransitionAllowed(string $from, string $to): bool
    {
        return in_array($to, $this->getTransitions($from));
    }

    protected function getTransitions(string $from): array
    {
        return $this->allowedTransitions[$from] ?? [];
    }
}
