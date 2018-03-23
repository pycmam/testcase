<?php

namespace App\Command;

use App\Repository\AccountRepository;
use App\Repository\LockRepository;
use App\Repository\OperationRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppBalanceShowCommand extends Command
{
    protected static $defaultName = 'app:balance:show';

    private $accountRepository;
    private $operationRepository;
    private $lockRepository;


    public function __construct(
        AccountRepository $accountRepository,
        OperationRepository $operationRepository,
        LockRepository $lockRepository
    ) {
        $this->accountRepository = $accountRepository;
        $this->operationRepository = $operationRepository;
        $this->lockRepository = $lockRepository;

        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setDescription('Show account balance ')
            ->addArgument('account', InputArgument::REQUIRED, 'Account ID');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $account = $this->accountRepository->find($input->getArgument('account'));

        if (!$account) {
            throw new \InvalidArgumentException('Account not found');
        }

        $output->writeln('      Account: ' . $account);

        $output->writeln('        Total: ' . $this->accountRepository->getAccountTotalBalance($account));
        $output->writeln('    Available: ' . $this->accountRepository->getAccountAvailableBalance($account));
        $output->writeln('Locked to out: ' . $this->accountRepository->getAccountLockedOutBalance($account));
        $output->writeln(' Locked to in: ' . $this->accountRepository->getAccountLockedInBalance($account));

        $output->writeln('');
        $output->writeln('------- Locks -------');

        foreach ($this->lockRepository->getAccountLocks($account) as $lock) {
            $output->writeln((string)$lock);
        }

        $output->writeln('');
        $output->writeln('------- Operations -------');

        foreach ($this->operationRepository->getAccountOperations($account) as $operation) {
            $output->writeln((string)$operation);
        }
    }
}
