<?php

namespace App\Service\Receipt;

use App\Dictionary\ReceiptStatusDictionary;
use App\DTO\NoneReferenceReceiptData;
use App\Entity\Receipt;
use Doctrine\ORM\EntityManagerInterface;

class NoneReferenceReceiptService
{
    public function __construct(private EntityManagerInterface $manager, private NoneReferenceReceiptFactory $factory)
    {
    }

    public function makeReceipt(NoneReferenceReceiptData $data): Receipt
    {
        $receipt = $this->factory->create($data->getType());

        $receipt->setStatus(ReceiptStatusDictionary::DRAFT);

        $this->fillData($receipt, $data);

        $this->manager->persist($receipt);
        $this->manager->flush();

        return $receipt;
    }

    public function updateReceipt(Receipt $receipt, NoneReferenceReceiptData $data): Receipt
    {
        $this->fillData($receipt, $data);

        $this->manager->flush();

        return $receipt;
    }

    private function fillData(Receipt $receipt, NoneReferenceReceiptData $data): void
    {
        $receipt->setSourceWarehouse($data->getSourceWarehouse())
                ->setDestinationWarehouse($data->getDestinationWarehouse())
                ->setCostCenter($data->getCostCenter())
                ->setDescription($data->getDescription());
    }
}
