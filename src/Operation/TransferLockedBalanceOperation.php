<?php

namespace App\Operation;

use App\Event\BalanceTransferLocked;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TransferLockedBalanceOperation extends TransferBalanceOperation implements BalanceOperationInterface
{

    /**
     * @return bool
     * @throws \App\Exception\AccountIsBusyException
     * @throws \App\Exception\NotEnoughBalanceException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function execute(): bool
    {
        try {

            $this->lockAccounts([$this->getSource(), $this->getDestination()]);
            $this->validateAvailableBalance($this->getSource(), $this->getAmount());

            $lock = $this->lockRepository->create($this->getSource(), $this->getDestination(), $this->getAmount());

            $result = (bool)$lock->getId();

            if ($result) {
                $this->dispatcher->dispatch(BalanceTransferLocked::NAME, new BalanceTransferLocked($lock));
            }
        } finally {
            $this->releaseAccounts([$this->getSource(), $this->getDestination()]);
        }

        return $result;
    }
}