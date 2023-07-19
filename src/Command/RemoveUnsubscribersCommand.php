<?php

namespace App\Command;

use App\Repository\SubscriberRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:unsubscribe')]
class RemoveUnsubscribersCommand extends Command
{
    private SubscriberRepository $subscriberRepository;

    public function __construct(SubscriberRepository $subscriberRepository)
    {
        $this->subscriberRepository = $subscriberRepository;
        parent::__construct();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Unsubscribe account',
            '============',
            '',
        ]);
        $total = $this->subscriberRepository->removeDisableSubscribers();

        $output->write(sprintf('%d subscribers deleted !', $total));

        return Command::SUCCESS;
    }
}
