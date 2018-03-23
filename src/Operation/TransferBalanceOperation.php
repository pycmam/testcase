<?php

namespace App\Operation;

use App\Event\BalanceTransferSuccess;
use App\Exception\AccountIsBusyException;
use App\Exception\InvalidOperationParameterException;

class TransferBalanceOperation extends BalanceOperation implements BalanceOperationInterface
{
    /**
     * @return bool
     * @throws AccountIsBusyException
     * @throws \App\Exception\NotEnoughBalanceException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Throwable
     */
    public function execute(): bool
    {
        try {

            $this->lockAccount($this->params['source']);
            $this->lockAccount($this->params['destination']);
            $this->validateAvailableBalance($this->params['source'], $this->params['amount']);

            $operations = $this->operationRepository->transactional(function () {
                $sourceOp = $this->operationRepository->create($this->params['source'], -1 * $this->params['amount']);
                $destOp = $this->operationRepository->create($this->params['destination'], $this->params['amount']);

                return [$sourceOp, $destOp];
            });

            $result = false !== $operations || is_array($operations);

            if ($result) {
                $this->dispatcher->dispatch('balance.transfer', new BalanceTransferSuccess($operations));
            }
        } finally {
            $this->releaseAccount($this->params['source']);
            $this->releaseAccount($this->params['destination']);
        }

        return $result;
    }


    /**
     * @param array $values
     *
     * @return BalanceOperationInterface
     */
    public function bindParams(array $values): BalanceOperationInterface
    {
        parent::bindParams($values);

        if ($this->params['source']->getId() == $this->params['destination']->getId()) {
            throw new InvalidOperationParameterException("Source and destination account is same!");
        }

        return $this;
    }


    public function getValidators()
    {
        return [
            'source' => function ($value) {

                $account = $this->accountRepository->find($value);

                if (!$account) {
                    throw new InvalidOperationParameterException("Account with id={$value} not found");
                }

                return $account;
            },

            'destination' => function ($value) {

                $account = $this->accountRepository->find($value);

                if (!$account) {
                    throw new InvalidOperationParameterException("Account with id={$value} not found");
                }

                return $account;
            },

            'amount' => function ($value) {

                $value = (int)$value;

                if ($value <= 0) {
                    throw new InvalidOperationParameterException("Amount must be greater than 0");
                }

                return $value;
            },
        ];
    }

}