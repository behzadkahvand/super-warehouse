<?php

namespace App\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

trait Blameable
{
    /**
     * @var string
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(type="string", nullable=true)
     *
     * @Groups({"blameable"})
     */
    protected $createdBy;

    /**
     * @var string
     * @Gedmo\Blameable(on="update")
     * @ORM\Column(type="string", nullable=true)
     *
     * @Groups({"blameable"})
     */
    protected $updatedBy;

    public function setCreatedBy($createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy($updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): string
    {
        return $this->updatedBy;
    }
}
