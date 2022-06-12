<?php

namespace App\Service\MongoFilter;

use App\Service\Pipeline\Pipeline;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Query\Builder;

class PipelineMongoQueryBuilder
{
    private DocumentManager $documentManager;

    private iterable $stages;

    public function __construct(iterable $stages, DocumentManager $documentManager)
    {
        $this->stages = $stages;
        $this->documentManager = $documentManager;
    }

    public function filter(string $sourceClass, array $requestFilters = []): Builder
    {
        $pipeline = Pipeline::fromStages($this->stages);

        $payload = (new FilterPayload())
            ->setQueryBuilder($this->documentManager->createQueryBuilder($sourceClass))
            ->setRequestFilters($requestFilters);

        /** @var FilterPayload $finalPayload */
        $finalPayload = $pipeline->process($payload);

        return $finalPayload->getQueryBuilder();
    }
}
