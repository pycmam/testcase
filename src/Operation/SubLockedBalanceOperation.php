<?php

namespace App\Operation;

use App\Event\BalanceSubLocked;

class SubLockedBalanceOperation extends AccountBalanceOperation implements BalanceOperationInterface
{
    /**
     * @return bool
     * @throws \App\Exception\AccountIsBusyException
     * @throws \App\Exception\NotEnoughBalanceException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function execute(): bool
    {
        try {
            $this->lockAccount($this->params['account']);

            $this->validateAvailableBalance($this->params['account'], $this->params['amount']);

            $lock = $this->lockRepository->create($this->params['account'], null, $this->params['amount']);

            $result = (bool)$lock->getId();

            if ($result) {
                $this->dispatcher->dispatch('balance.sub.locked', new BalanceSubLocked($lock));
            }
        } finally {
            $this->releaseAccount($this->params['account']);
        }

        return $result;
    }
}