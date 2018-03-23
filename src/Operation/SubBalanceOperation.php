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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function execute(): bool
    {
        try {
            $this->lockAccounts([$this->getAccount()]);

            $this->validateAvailableBalance($this->getAccount(), $this->getAmount());

            $operation = $this->operationRepository->create($this->getAccount(), -1 * $this->getAmount());

            $result = (bool)$operation->getId();

            if ($result) {
                $this->dispatcher->dispatch(BalanceSubSuccess::NAME,
                    new BalanceSubSuccess(['sub' => $operation]));
            }
        } finally {
            $this->releaseAccounts([$this->getAccount()]);
        }

        return $result;
    }

}