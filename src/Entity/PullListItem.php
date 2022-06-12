<?php

namespace App\Entity;

use App\DTO\AllowTransitionConfigData;
use App\DTO\StateSubscriberConfigData;
use App\Entity\Common\Blameable;
use App\Entity\Common\Timestampable;
use App\Repository\PullListItemRepository;
use App\Service\StatusTransition\AllowTransitions\PullList\PullListAllowedTransition;
use App\Service\StatusTransition\Subscribers\PullList\PullListStateDecisionMakerSubscriber;
use App\Service\StatusTransition\TransitionableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="pull_list_items")
 * @ORM\Entity(repositoryClass=PullListItemRepository::class)
 */
class PullListItem implements TransitionableInterface
{
    use Timestampable;
    use Blameable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({
     *     "pullList.items.add",
     *     "stow.hand-held.stowing",
     *     "pullList.items.index",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     * })
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=PullList::class, inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pullList;

    /**
     * @ORM\ManyToOne(targetEntity=Receipt::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"pullList.items.add", "pullList.items.index",})
     */
    private $receipt;

    /**
     * @ORM\OneToOne(targetEntity=ReceiptItem::class, inversedBy="pullListItem")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({
     *     "pullList.items.add",
     *     "pullList.items.index",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     * })
     */
    private $receiptItem;

    /**
     * @ORM\Column(type="integer")
     * @Groups({
     *     "pullList.items.add",
     *     "stow.hand-held.stowing",
     *     "pullList.items.index",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     * })
     */
    private $quantity;

    /**
     * @ORM\Column(type="integer")
     * @Groups({
     *     "pullList.items.add",
     *     "stow.hand-held.stowing",
     *     "pullList.items.index",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     * })
     */
    private $remainQuantity;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"pullList.items.add", "stow.hand-held.stowing", "pullList.items.index",})
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPullList(): ?PullList
    {
        return $this->pullList;
    }

    public function setPullList(?PullList $pullList): self
    {
        $this->pullList = $pullList;

        return $this;
    }

    public function getReceipt(): ?Receipt
    {
        return $this->receipt;
    }

    public function setReceipt(?Receipt $receipt): self
    {
        $this->receipt = $receipt;

        return $this;
    }

    public function getReceiptItem(): ?ReceiptItem
    {
        return $this->receiptItem;
    }

    public function setReceiptItem(?ReceiptItem $receiptItem): self
    {
        $this->receiptItem = $receiptItem;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getRemainQuantity(): ?int
    {
        return $this->remainQuantity;
    }

    public function setRemainQuantity(int $remainQuantity): self
    {
        $this->remainQuantity = $remainQuantity;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
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
        return (new StateSubscriberConfigData())
            ->addSubscriber(PullListStateDecisionMakerSubscriber::class);
    }
}
