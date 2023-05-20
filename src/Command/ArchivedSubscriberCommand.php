<?php

namespace App\Command;

use App\Service\ArchivedSubscriber;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:archive-subscribers')]
class ArchivedSubscriberCommand extends Command
{
    private ArchivedSubscriber $archivedSubscriber;
    protected static $defaultDescription = 'Archive properties';

    public function __construct(ArchivedSubscriber $archivedSubscriber)
    {
        $this->archivedSubscriber = $archivedSubscriber;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Archive subscribers',
            '============',
            '',
        ]);

        $this->archivedSubscriber->archived();

        $output->writeln([
            'Successfull archive',
        ]);

        return Command::SUCCESS;
    }
}
