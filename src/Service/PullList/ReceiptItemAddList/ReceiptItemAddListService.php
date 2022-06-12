<?php

namespace App\Service\PullList\ReceiptItemAddList;

use App\Service\Pipeline\Pipeline;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;

class ReceiptItemAddListService
{
    private iterable $stages;

    public function __construct(iterable $stages)
    {
        $this->stages = $stages;
    }

    public function get(SearchPayload $payload): AbstractQuery
    {
        $pipeline = Pipeline::fromStages($this->stages);

        return $pipeline->process($payload)
                        ->getQueryBuilder()
                        ->getQuery()
                        ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
    }
}
