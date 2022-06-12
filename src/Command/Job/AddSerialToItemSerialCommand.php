<?php

namespace App\Command\Job;

use App\Repository\ItemSerialRepository;
use App\Service\ItemSerial\Serial\AddSerialService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddSerialToItemSerialCommand extends Command
{
    protected static $defaultName = 'timcheh-warehouse:job:add-serial-to-item-serial';

    public function __construct(
        protected AddSerialService $addSerialService,
        protected ItemSerialRepository $itemSerialRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Add serial to item serials');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $serialItemIds = $this->itemSerialRepository->getItemSerialIdsHasNoSerial();

        $this->addSerialService->addMany($serialItemIds);

        $io->success('You have successfully added serials to item serials');

        return Command::SUCCESS;
    }
}
