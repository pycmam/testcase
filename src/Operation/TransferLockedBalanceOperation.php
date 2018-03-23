<?php

namespace App\Operation;

use App\Event\BalanceTransferLocked;

class TransferLockedBalanceOperation extends TransferBalanceOperation implements BalanceOperationInterface
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

            $this->lockAccount($this->params['source']);
            $this->lockAccount($this->params['destination']);
            $this->validateAvailableBalance($this->params['source'], $this->params['amount']);

            $lock = $this->lockRepository->create($this->params['source'], $this->params['destination'],
                $this->params['amount']);

            $result = (bool)$lock->getId();

            if ($result) {
                $this->dispatcher->dispatch('balance.transfer.locked', new BalanceTransferLocked([$lock]));
            }
        } finally {
            $this->releaseAccount($this->params['source']);
            $this->releaseAccount($this->params['destination']);
        }

        return $result;
    }
}