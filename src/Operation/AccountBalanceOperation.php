<?php

namespace App\Operation;

use App\Exception\InvalidOperationParameterException;

abstract class AccountBalanceOperation extends BalanceOperation
{

    public function getValidators()
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