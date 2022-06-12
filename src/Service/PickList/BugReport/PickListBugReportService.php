<?php

namespace App\Service\PickList\BugReport;

use App\Dictionary\PickListBugReportStatusDictionary;
use App\Dictionary\PickListStatusDictionary;
use App\Entity\PickList;
use App\Entity\PickListBugReport;
use App\Service\PickList\PickListService;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

final class PickListBugReportService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private PickListBugReportFactory $factory,
        private PickListService $pickListService
    ) {
    }

    public function create(PickList $pickList): ?PickListBugReport
    {
        $this->manager->beginTransaction();
        try {
            if ($this->canMakeNewBugReport($pickList)) {
                $bugReport = $this->makeNewBugReport($pickList);
            }

            $pickLists        = $this->pickListService->create($pickList->getReceiptItem());
            $assignedQuantity = !empty($pickLists) ?
                collect($pickLists)->sum(fn(PickList $pickList) => $pickList->getQuantity()) : 0;

            if ($assignedQuantity !== 0) {
                $this->updatePickList($pickList, $assignedQuantity);
            }

            $this->manager->flush();
            $this->manager->commit();
        } catch (Throwable $exception) {
            $this->manager->close();
            $this->manager->rollback();

            throw $exception;
        }

        return $bugReport ?? null;
    }

    private function canMakeNewBugReport(PickList $pickList): bool
    {
        return empty($pickList->getPickListBugReport());
    }

    private function makeNewBugReport(PickList $pickList): PickListBugReport
    {
        $bugReport = $this->factory->create();

        $bugReport->setStatus(PickListBugReportStatusDictionary::PENDING)
                  ->setQuantity($pickList->getRemainedQuantity())
                  ->setWarehouse($pickList->getReceiptItem()->getReceipt()->getSourceWarehouse())
                  ->setInventory($pickList->getReceiptItem()->getInventory())
                  ->setPickList($pickList);

        $this->manager->persist($bugReport);

        return $bugReport;
    }

    private function updatePickList(PickList $pickList, int $assignedQuantity): void
    {
        $pickList->setQuantity($pickList->getQuantity() - $assignedQuantity);
        if ($pickList->getRemainedQuantity() === 0) {
            $pickList->setStatus(PickListStatusDictionary::CLOSE);
        }
    }
}
