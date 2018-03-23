<?php

namespace App\Operation;

use App\Event\BalanceAddSuccess;
use App\Event\BalanceSubSuccess;
use App\Event\BalanceTransferSuccess;

class ApproveLockOperation extends LockOperation
{
    /**
     * @return bool
     * @throws \App\Exception\AccountIsBusyException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Throwable
     */
    public function execute(): bool
    {
        try {

            $this->lockAccounts([$this->getSource(), $this->getDestination()]);

            $operations = $this->operationRepository->transactional(
                function () {

                    if (!$this->lockRepository->approve($this->getLock())) {
                        throw new \RuntimeException("Cant approve lock with id={$this->getLock()->getId()}");
                    }

                    $operations = [];

                    if ($source = $this->getSource()) {
                        $operations['sub'] = $this->operationRepository->create(
                            $source, -1 * $this->getAmount());
                    }

                    if ($destination = $this->getDestination()) {
                        $operations['add'] = $this->operationRepository->create(
                            $destination, $this->getAmount());
                    }

                    return $operations;
                });



            // dispatch events

            if (isset($operations['sub'], $operations['add'])) {
                $this->dispatcher->dispatch(BalanceTransferSuccess::NAME,
                    new BalanceTransferSuccess(array_values($operations)));
            } elseif (isset($operations['sub'])) {
                $this->dispatcher->dispatch(BalanceSubSuccess::NAME,
                    new BalanceSubSuccess([$operations['sub']]));
            } elseif (isset($operations['add'])) {
                $this->dispatcher->dispatch(BalanceAddSuccess::NAME,
                    new BalanceAddSuccess([$operations['add']]));
            }

            return true;

        } finally {
            $this->releaseAccounts([$this->getSource(), $this->getDestination()]);
        }

        return false;
    }

}