<?php

namespace App\Messaging\Handlers\Command\ItemSerial;

use App\Entity\ItemSerial;
use App\Messaging\Messages\Command\ItemSerial\AddSerialToItemSerial;
use App\Service\Utils\SerialGenerator\SerialGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddSerialToItemSerialHandler implements MessageHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected SerialGeneratorService $serialGenerator
    ) {
    }

    public function __invoke(AddSerialToItemSerial $addSerialToItemSerial): void
    {
        $itemSerialId = $addSerialToItemSerial->getItemSerialId();

        $itemSerial = $this->entityManager->getReference(ItemSerial::class, $itemSerialId);

        if (!$itemSerial) {
            $this->logger->error(
                sprintf('It can not add serial to item serial %d when item serial not exist!', $itemSerialId)
            );

            return;
        }

        $serial = $this->serialGenerator->encode($itemSerialId);

        /**
         * @var ItemSerial $itemSerial
         */
        $itemSerial->setSerial($serial);

        $this->entityManager->flush();
    }
}
