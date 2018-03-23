<?php

namespace App\Operation;

use App\Entity\Account;
use App\Event\BalanceTransferSuccess;
use App\Exception\AccountIsBusyException;
use App\Exception\InvalidOperationParameterException;

class TransferBalanceOperation extends BalanceOperation implements BalanceOperationInterface
{

    /**
     * @return bool
     * @throws AccountIsBusyException
     * @throws \App\Exception\NotEnoughBalanceException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Throwable
     */
    public function execute(): bool
    {
        try {

            $this->lockAccounts([$this->getSource(), $this->getDestination()]);
            $this->validateAvailableBalance($this->getSource(), $this->getAmount());

            $operations = $this->operationRepository->transactional(function () {

                $subOp = $this->operationRepository->create($this->getSource(), -1 * $this->getAmount());
                $addOp = $this->operationRepository->create($this->getDestination(), $this->getAmount());

                return ['sub' => $subOp, 'add' => $addOp];
            });

            $result = false !== $operations || is_array($operations);

            if ($result) {
                $this->dispatcher->dispatch(BalanceTransferSuccess::NAME,
                    new BalanceTransferSuccess($operations));
            }
        } finally {
            $this->releaseAccounts([$this->getSource(), $this->getDestination()]);
        }

        return $result;
    }


    /**
     * @return int
     */
    protected function getAmount(): int
    {
        return $this->params['amount'];
    }


    /**
     * @return Account
     */
    protected function getSource(): Account
    {
        return $this->params['source'];
    }


    /**
     * @return Account
     */
    protected function getDestination(): Account
    {
        return $this->params['destination'];
    }


    /**
     * @param array $values
     *
     * @return BalanceOperationInterface
     */
    public function bindParams(array $values): BalanceOperationInterface
    {
        parent::bindParams($values);

        if ($this->getSource()->getId() == $this->getDestination()->getId()) {
            throw new InvalidOperationParameterException("Source and destination account is same!");
        }

        return $this;
    }


    /**
     * @return array
     */
    public function getValidators(): array
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