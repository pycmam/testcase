<?php

namespace App\EventListener;

use App\Entity\Operation;
use App\Event\BalanceAddLocked;
use App\Event\BalanceAddSuccess;
use App\Event\BalanceSubLocked;
use App\Event\BalanceSubSuccess;
use App\Event\BalanceTransferLocked;
use App\Event\BalanceTransferSuccess;
use Psr\Log\LoggerInterface;

class BalanceEventListener
{
    private $logger;


    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    public function onAddSuccess(BalanceAddSuccess $event)
    {
        /**
         * @var Operation $operation
         */
        $operation = $event->getOperations()['add'];

        $this->logger->info(sprintf('Added %d coins to %s',
            $operation->getAmount(), $operation->getAccount()->getUsername()));
    }


    public function onSubSuccess(BalanceSubSuccess $event)
    {
        /**
         * @var Operation $operation
         */
        $operation = $event->getOperations()['sub'];

        $this->logger->info(sprintf('Sub %d coins from %s',
            abs($operation->getAmount()), $operation->getAccount()->getUsername()));
    }


    public function onTransferSuccess(BalanceTransferSuccess $event)
    {
        /**
         * @var Operation $subOperation
         * @var Operation $addOperation
         */
        $subOperation = $event->getOperations()['sub'];
        $addOperation = $event->getOperations()['add'];

        $this->logger->info(sprintf('Transfer %d coins from %s to %s', $addOperation->getAmount(),
            $subOperation->getAccount()->getUsername(), $addOperation->getAccount()->getUsername()));
    }


    public function onAddLocked(BalanceAddLocked $event)
    {
        $this->logger->info(sprintf('Locked add %d coins to %s',
            $event->getLock()->getAmount(), $event->getLock()->getDestination()->getUsername()));
    }


    public function onSubLocked(BalanceSubLocked $event)
    {
        $this->logger->info(sprintf('Locked sub %d coins from %s',
            $event->getLock()->getAmount(), $event->getLock()->getSource()->getUsername()));
    }


    public function onTransferLocked(BalanceTransferLocked $event)
    {
        $lock = $event->getLock();

        $this->logger->info(sprintf('Transfer %d coins from %s to %s', $lock->getAmount(),
            $lock->getSource()->getUsername(), $lock->getDestination()->getUsername()));
    }
}