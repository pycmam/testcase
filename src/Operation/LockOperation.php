<?php

namespace App\Operation;

use App\Entity\Account;
use App\Entity\Lock;
use App\Exception\InvalidOperationParameterException;

abstract class LockOperation extends BalanceOperation
{
    /**
     * @return Lock
     */
    protected function getLock(): Lock
    {
        return $this->params['lock'];
    }


    /**
     * @return Account|null
     */
    protected function getSource(): ?Account
    {
        return $this->getLock()->getSource();
    }


    /**
     * @return Account|null
     */
    protected function getDestination(): ?Account
    {
        return $this->getLock()->getDestination();
    }


    /**
     * @return int
     */
    protected function getAmount(): int
    {
        return $this->getLock()->getAmount();
    }


    /**
     * @return array
     */
    public function getValidators(): array
    {
        return [
            'lock' => function ($value) {

                /**
                 * @var Lock $lock
                 */
                $lock = $this->lockRepository->find($value);

                if (!$lock) {
                    throw new InvalidOperationParameterException("Lock {$value} not found");
                }

                if ($lock->getApproved()) {
                    throw new InvalidOperationParameterException("Lock {$lock->getId()} already approved");
                }

                return $lock;
            },
        ];
    }

}