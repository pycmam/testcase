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
     */
    public function execute(): bool
    {
        try {
            $this->lockAccount($this->params['account']);

            $lock = $this->lockRepository->create(null, $this->params['account'], $this->params['amount']);

            $result = (bool)$lock->getId();

            if ($result) {
                $this->dispatcher->dispatch('balance.add.locked', new BalanceAddLocked([$lock]));
            }
        } finally {
            $this->releaseAccount($this->params['account']);
        }

        return $result;
    }

}