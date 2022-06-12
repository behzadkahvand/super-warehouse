<?php

namespace App\Entity;

use App\DTO\AllowTransitionConfigData;
use App\DTO\StateSubscriberConfigData;
use App\Entity\Common\Blameable;
use App\Entity\Common\Timestampable;
use App\Repository\PullListRepository;
use App\Service\StatusTransition\AllowTransitions\PullList\PullListAllowedTransition;
use App\Service\StatusTransition\TransitionableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Table(name="pull_lists")
 * @ORM\Entity(repositoryClass=PullListRepository::class)
 */
class PullList implements TransitionableInterface
{
    use Timestampable;
    use Blameable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({
     *     "pullList.manual.store",
     *     "pullList.read",
     *     "pullList.items.add",
     *     "pullList.locator.assign",
     *     "stow.hand-held.stowing",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     * })
     */
    private $id;

    /**
     * @Assert\NotBlank(groups={"pullList.manual.store","pullList.update",})
     * @ORM\ManyToOne(targetEntity=Warehouse::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({
     *     "pullList.read",
     *     "pullList.items.add",
     *     "pullList.locator.assign",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     * })
     */
    private $warehouse;

    /**
     * @Assert\Choice(
     *     groups={"pullList.manual.store","pullList.update",},
     *     callback={"App\Dictionary\PullListPriorityDictionary", "toArray"}
     * )
     * @Assert\NotBlank(groups={"pullList.manual.store","pullList.update",})
     * @ORM\Column(type="string", length=50)
     * @Groups({
     *     "pullList.read",
     *     "pullList.items.add",
     *     "pullList.locator.assign",
     *     "stow.hand-held.stowing",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     * })
     */
    private $priority;

    /**
     * @Assert\NotBlank(groups={"pullList.locator.assign",})
     * @ORM\ManyToOne(targetEntity=Admin::class, inversedBy="pullLists")
     * @Groups({"pullList.read", "pullList.locator.assign",})
     */
    private $locator;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"pullList.read", "pullList.items.add", "stow.hand-held.stowing",})
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=PullListItem::class, mappedBy="pullList", orphanRemoval=true)
     * @Groups({
     *     "pullList.items.add",
     *     "stow.hand-held.stowing",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     * })
     */
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getLocator(): ?Admin
    {
        return $this->locator;
    }

    public function setLocator(?Admin $locator): self
    {
        $this->locator = $locator;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|PullListItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(PullListItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setPullList($this);
        }

        return $this;
    }

    public function removeItem(PullListItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getPullList() === $this) {
                $item->setPullList(null);
            }
        }

        return $this;
    }

    /**
     * @Groups({"pullList.read", "pull-list.hand-held.active-for-locate","pull-list.hand-held.show-active-list",})
     * @SerializedName("quantity")
     */
    public function getQuantity(): int
    {
        $quantity = 0;

        foreach ($this->getItems() as $item) {
            $quantity += $item->getQuantity();
        }

        return $quantity;
    }

    /**
     * @Groups({"pullList.read", "pull-list.hand-held.active-for-locate","pull-list.hand-held.show-active-list",})
     * @SerializedName("remainingQuantity")
     */
    public function getRemainingQuantity(): int
    {
        $remaining = 0;

        foreach ($this->getItems() as $item) {
            $remaining += $item->getRemainQuantity();
        }

        return $remaining;
    }

    public function getStatePropertyName(): string
    {
        return "status";
    }

    public function getAllowedTransitions(): AllowTransitionConfigData
    {
        return (new PullListAllowedTransition())->__invoke();
    }

    public function getStateSubscribers(): StateSubscriberConfigData
    {
        return (new StateSubscriberConfigData());
    }

    public function getItemsCount(): int
    {
        return $this->getItems()->count();
    }

    public function hasItems(): bool
    {
        return $this->getItemsCount() > 0;
    }
}
