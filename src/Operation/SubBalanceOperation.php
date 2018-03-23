<?php

namespace App\Operation;

use App\Event\BalanceSubSuccess;
use App\Exception\AccountIsBusyException;

class SubBalanceOperation extends AccountBalanceOperation implements BalanceOperationInterface
{
    /**
     * @return bool
     * @throws AccountIsBusyException
     * @throws \App\Exception\NotEnoughBalanceException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function execute(): bool
    {
        try {
            $this->lockAccount($this->params['account']);

            $this->validateAvailableBalance($this->params['account'], $this->params['amount']);

            $operation = $this->operationRepository->create($this->params['account'], -1 * $this->params['amount']);

            $result = (bool)$operation->getId();

            if ($result) {
                $this->dispatcher->dispatch('balance.add', new BalanceSubSuccess([$operation]));
            }
        } finally {
            $this->releaseAccount($this->params['account']);
        }

        return $result;
    }

}