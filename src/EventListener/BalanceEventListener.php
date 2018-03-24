<?php

namespace App\EventListener;

use App\Entity\Operation;
use App\Event\BalanceAddLocked;
use App\Event\BalanceAddSuccess;
use App\Event\BalanceLockApproved;
use App\Event\BalanceLockRemoved;
use App\Event\BalanceSubLocked;
use App\Event\BalanceSubSuccess;
use App\Event\BalanceTransferLocked;
use App\Event\BalanceTransferSuccess;
use Psr\Log\LoggerInterface;

/**
 * Class BalanceEventListener
 * @package App\EventListener
 */
class BalanceEventListener
{
    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * BalanceEventListener constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @param BalanceAddSuccess $event
     */
    public function onBalanceAddSuccess(BalanceAddSuccess $event)
    {
        /**
         * @var Operation $operation
         */
        $operation = $event->getOperations()['add'];

        $this->logger->info(sprintf('Add %d coins to %s SUCCESS',
            $operation->getAmount(), $operation->getAccount()->getUsername()));
    }


    /**
     * @param BalanceSubSuccess $event
     */
    public function onBalanceSubSuccess(BalanceSubSuccess $event)
    {
        /**
         * @var Operation $operation
         */
        $operation = $event->getOperations()['sub'];

        $this->logger->info(sprintf('Sub %d coins from %s SUCCESS',
            abs($operation->getAmount()), $operation->getAccount()->getUsername()));
    }


    /**
     * @param BalanceTransferSuccess $event
     */
    public function onBalanceTransferSuccess(BalanceTransferSuccess $event)
    {
        /**
         * @var Operation $subOperation
         * @var Operation $addOperation
         */
        $subOperation = $event->getOperations()['sub'];
        $addOperation = $event->getOperations()['add'];

        $this->logger->info(sprintf('Transfer %d coins from %s to %s SUCCESS', $addOperation->getAmount(),
            $subOperation->getAccount()->getUsername(), $addOperation->getAccount()->getUsername()));
    }


    /**
     * @param BalanceAddLocked $event
     */
    public function onBalanceAddLocked(BalanceAddLocked $event)
    {
        $this->logger->info(sprintf('Add %d coins to %s LOCKED',
            $event->getLock()->getAmount(), $event->getLock()->getDestination()->getUsername()));
    }


    /**
     * @param BalanceSubLocked $event
     */
    public function onBalanceSubLocked(BalanceSubLocked $event)
    {
        $this->logger->info(sprintf('Sub %d coins from %s LOCKED',
            $event->getLock()->getAmount(), $event->getLock()->getSource()->getUsername()));
    }


    /**
     * @param BalanceTransferLocked $event
     */
    public function onBalanceTransferLocked(BalanceTransferLocked $event)
    {
        $lock = $event->getLock();

        $this->logger->info(sprintf('Transfer %d coins from %s to %s LOCKED', $lock->getAmount(),
            $lock->getSource()->getUsername(), $lock->getDestination()->getUsername()));
    }


    /**
     * @param BalanceLockApproved $event
     */
    public function onBalanceLockApproved(BalanceLockApproved $event)
    {
        $this->logger->info(sprintf('Lock %d APPROVED', $event->getLock()->getId()));
    }


    /**
     * @param BalanceLockRemoved $event
     */
    public function onBalanceLockRemoved(BalanceLockRemoved $event)
    {
        $this->logger->info(sprintf('Lock %d REMOVED', $event->getLock()->getId()));
    }

}