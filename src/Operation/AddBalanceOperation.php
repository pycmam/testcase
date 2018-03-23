<?php

namespace App\Operation;

use App\Event\BalanceAddSuccess;
use App\Exception\AccountIsBusyException;

class AddBalanceOperation extends AccountBalanceOperation implements BalanceOperationInterface
{

    /**
     * @return bool
     * @throws AccountIsBusyException
     */
    public function execute(): bool
    {
        try {
            $this->lockAccount($this->params['account']);

            $operation = $this->operationRepository->create($this->params['account'], $this->params['amount']);

            $result = (bool)$operation->getId();

            if ($result)
            {
                $this->dispatcher->dispatch('balance.add', new BalanceAddSuccess([$operation]));
            }

        } finally {
            $this->releaseAccount($this->params['account']);
        }

        return $result;
    }

}