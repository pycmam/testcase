<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppLockApproveCommand extends BaseBalanceCommand
{
    protected static $defaultName = 'app:lock:approve';


    protected function configure()
    {
        $this
            ->setDescription('Approve locked balance operation')
            ->addArgument('lock', InputArgument::REQUIRED, 'Lock ID');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $message = [
            'operation' => $operation = 'ApproveLock',
            'params'    => [
                'lock' => $lock = $input->getArgument('lock'),
            ],
        ];

        $this->producer->publish(json_encode($message));

        $output->writeln('ApproveLock: ID ' . $lock);
    }
}
