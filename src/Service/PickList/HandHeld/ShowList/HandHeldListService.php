<?php

namespace App\Service\PickList\HandHeld\ShowList;

use App\Dictionary\PickListStatusDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\PickList;
use App\Repository\PickListRepository;
use App\Service\StatusTransition\StateTransitionHandlerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Throwable;

class HandHeldListService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private StateTransitionHandlerService $transitionHandlerService,
        private PickListRepository $pickListRepository
    ) {
    }

    public function getList(string $receiptType): array
    {
        $pickerActivePickList = $this->pickListRepository->getPickerAllActivePickList($this->security->getUser());

        if ($pickerActivePickList) {
            return $pickerActivePickList;
        }

        return $this->pickListRepository->getHandHeldPickList($receiptType);
    }

    public function confirmList(ArrayCollection $pickLists): void
    {
        $this->entityManager->beginTransaction();

        try {
            $receiptItems = [];

            /** @var PickList $pickList */
            foreach ($pickLists as $pickList) {
                $pickList->setPicker($this->security->getUser());

                $receiptItems[$pickList->getReceiptItem()->getId()] = $pickList->getReceiptItem();
            }

            $this->transitionHandlerService->batchTransitState(
                $pickLists->toArray(),
                PickListStatusDictionary::PICKING
            );

            $this->transitionHandlerService->batchTransitState(
                array_values($receiptItems),
                ReceiptStatusDictionary::PICKING
            );

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->close();
            $this->entityManager->rollback();

            throw $exception;
        }
    }
}
