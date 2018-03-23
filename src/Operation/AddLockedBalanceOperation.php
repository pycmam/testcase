<?php

namespace App\Operation;

use App\Event\BalanceAddLocked;

class AddLockedBalanceOperation extends AccountBalanceOperation implements BalanceOperationInterface
{
    /**
     * @return bool
     * @throws \App\Exception\AccountIsBusyException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function execute(): bool
    {
        try {
            $this->lockAccounts([$this->getAccount()]);

            $lock = $this->lockRepository->create(null, $this->getAccount(), $this->getAmount());

            $result = (bool)$lock->getId();

            if ($result) {
                $this->dispatcher->dispatch(BalanceAddLocked::NAME, new BalanceAddLocked($lock));
            }
        } finally {
            $this->releaseAccounts([$this->getAccount()]);
        }

        return $result;
    }

}