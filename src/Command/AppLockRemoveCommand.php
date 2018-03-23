<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppLockRemoveCommand extends BaseBalanceCommand
{
    protected static $defaultName = 'app:lock:remove';


    protected function configure()
    {
        $this
            ->setDescription('Remove locked balance operation')
            ->addArgument('lock', InputArgument::REQUIRED, 'Lock ID');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $message = [
            'operation' => $operation = 'RemoveLock',
            'params'    => [
                'lock' => $lock = $input->getArgument('lock'),
            ],
        ];

        $this->producer->publish(json_encode($message));

        $output->writeln('RemoveLock: ID ' . $lock);
    }
}
