<?php

namespace App\Document\StatusTransitionLog;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/** @MongoDB\EmbeddedDocument */
class AdminLogData
{
    /**
     * @MongoDB\Field(name="id",type="int")
     */
    protected $id;

    /**
     * @MongoDB\Field(name="name",type="string")
     */
    protected $name;

    /**
     * @MongoDB\Field(name="username",type="string")
     */
    protected $username;

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
