<?php

namespace App\Operation;

use App\Event\BalanceSubLocked;

class SubLockedBalanceOperation extends AccountBalanceOperation implements BalanceOperationInterface
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
            $this->lockAccounts([$this->getAccount()]);

            $this->validateAvailableBalance($this->getAccount(), $this->getAmount());

            $lock = $this->lockRepository->create($this->params['account'], null, $this->getAmount());

            $result = (bool)$lock->getId();

            if ($result) {
                $this->dispatcher->dispatch(BalanceSubLocked::NAME,
                    new BalanceSubLocked($lock));
            }
        } finally {
            $this->releaseAccounts([$this->getAccount()]);
        }

        return $result;
    }
}