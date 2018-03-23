<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppBalanceAddCommand extends BaseBalanceCommand
{
    protected static $defaultName = 'app:balance:add';


    protected function configure()
    {
        $this
            ->setDescription('Add amount to account balance')
            ->addArgument('account', InputArgument::REQUIRED, 'Account ID')
            ->addArgument('amount', InputArgument::REQUIRED, 'Amount to add')
            ->addOption('lock', null, InputOption::VALUE_OPTIONAL, 'Lock operation', false);
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lock = $input->getOption('lock');

        $message = [
            'operation' => $operation = 'Add'. ($lock ? 'Locked' : ''),
            'params'    => [
                'account' => $account = $input->getArgument('account'),
                'amount'  => $amount = $input->getArgument('amount'),
            ],
        ];

        $this->producer->publish(json_encode($message));

        $output->writeln(sprintf('Add: %d coins to %d, state: %s',
            $amount, $account, $lock ? 'locked' : 'approved'));
    }
}
