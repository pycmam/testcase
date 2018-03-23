<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppBalanceTransferCommand extends BaseBalanceCommand
{
    protected static $defaultName = 'app:balance:transfer';


    protected function configure()
    {
        $this
            ->setDescription('Transfer amount from users')
            ->addArgument('source', InputArgument::REQUIRED, 'Source account ID')
            ->addArgument('destination', InputArgument::REQUIRED, 'Destination account ID')
            ->addArgument('amount', InputArgument::REQUIRED, 'Amount to transfer')
            ->addOption('lock', null, InputOption::VALUE_OPTIONAL, 'Lock operation', false);
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lock = $input->getOption('lock');

        $message = [
            'operation' => 'Transfer' . ($lock ? 'Locked' : ''),
            'params'    => [
                'source'      => $source = $input->getArgument('source'),
                'destination' => $destination = $input->getArgument('destination'),
                'amount'      => $amount = $input->getArgument('amount'),
            ],
        ];

        $this->producer->publish(json_encode($message));

        $output->writeln(sprintf('Transfer: %d coins from %d to %d, state: %s',
            $amount, $source, $destination, $lock ? 'locked' : 'approved'));
    }
}
