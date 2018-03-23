<?php

namespace App\Operation;

use App\Entity\Account;
use App\Exception\InvalidOperationParameterException;

abstract class AccountBalanceOperation extends BalanceOperation
{

    /**
     * @return Account
     */
    protected function getAccount(): Account
    {
        return $this->params['account'];
    }


    /**
     * @return int
     */
    protected function getAmount(): int
    {
        return $this->params['amount'];
    }


    /**
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getValidators(): array
    {
        return [
            'account' => function ($value) {

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