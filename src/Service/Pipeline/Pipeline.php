<?php

namespace App\Service\Pipeline;

class Pipeline implements PipelineInterface
{
    private iterable $stages;

    public function __construct(iterable $stages)
    {
        $this->stages = $stages;
    }

    public static function fromStages(iterable $stages): self
    {
        return new static($stages);
    }

    public function pipe(callable $stage): PipelineInterface
    {
        $pipeline = clone $this;
        $pipeline->stages[] = $stage;

        return $pipeline;
    }

    public function process(AbstractPipelinePayload $payload): AbstractPipelinePayload
    {
        foreach ($this->stages as $stage) {
            $payload = $stage($payload);
        }

        return $payload;
    }

    public function __invoke(AbstractPipelinePayload $payload): AbstractPipelinePayload
    {
        return $this->process($payload);
    }
}
