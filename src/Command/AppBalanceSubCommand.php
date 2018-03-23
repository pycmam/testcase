<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppBalanceSubCommand extends BaseBalanceCommand
{
    protected static $defaultName = 'app:balance:sub';


    protected function configure()
    {
        $this
            ->setDescription('Sub amount from account balance')
            ->addArgument('account', InputArgument::REQUIRED, 'Account ID')
            ->addArgument('amount', InputArgument::REQUIRED, 'Amount to sub')
            ->addOption('lock', null, InputOption::VALUE_OPTIONAL, 'Lock operation', false);
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lock = $input->getOption('lock');

        $message = [
            'operation' => $operation = 'Sub' . ($lock ? 'Locked' : ''),
            'params'    => [
                'account' => $account = $input->getArgument('account'),
                'amount'  => $amount = $input->getArgument('amount'),
            ],
        ];

        $this->producer->publish(json_encode($message));

        $output->writeln(sprintf('Sub: %d coins from %d, state: %s',
            $amount, $account, $lock ? 'locked' : 'approved'));
    }
}
