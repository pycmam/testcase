<?php

namespace App\Operation;

use App\Event\BalanceAddSuccess;
use App\Exception\AccountIsBusyException;

class AddBalanceOperation extends AccountBalanceOperation implements BalanceOperationInterface
{

    /**
     * @return bool
     * @throws AccountIsBusyException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function execute(): bool
    {
        try {
            $this->lockAccounts([$this->getAccount()]);

            $operation = $this->operationRepository->create($this->getAccount(), $this->getAmount());

            $result = (bool)$operation->getId();

            if ($result)
            {
                $this->dispatcher->dispatch(BalanceAddSuccess::NAME,
                    new BalanceAddSuccess(['add' => $operation]));
            }

        } finally {
            $this->releaseAccounts([$this->getAccount()]);
        }

        return $result;
    }

}