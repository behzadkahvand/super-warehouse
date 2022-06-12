<?php

namespace App\Document\StatusTransitionLog;

use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/** @MongoDB\MappedSuperclass */
abstract class StatusTransitionLog
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(name="entity_id",type="int")
     */
    protected $entityId;

    /**
     * @MongoDB\Field(name="status_from",type="string")
     */
    protected $statusFrom;

    /**
     * @MongoDB\Field(name="status_to",type="string")
     */
    protected $statusTo;


    /** @MongoDB\EmbedOne(targetDocument=AdminLogData::class) */
    protected $updatedBy;

    /**
     * @MongoDB\Field(name="updated_at",type="date")
     */
    protected $updatedAt;

    public function getId()
    {
        return $this->id;
    }

    public function setEntityId(int $entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function setStatusFrom(string $statusFrom): self
    {
        $this->statusFrom = $statusFrom;

        return $this;
    }

    public function getStatusFrom(): string
    {
        return $this->statusFrom;
    }

    public function setStatusTo(string $statusTo): self
    {
        $this->statusTo = $statusTo;

        return $this;
    }

    public function getStatusTo(): self
    {
        return $this->statusTo;
    }

    public function setUpdatedBy(?AdminLogData $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?AdminLogData
    {
        return $this->updatedBy;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }
}
