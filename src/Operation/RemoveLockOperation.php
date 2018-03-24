<?php

namespace App\Operation;

use App\Event\BalanceLockRemoved;

class RemoveLockOperation extends LockOperation
{

    public function execute(): bool
    {
        try {
            $this->lockAccounts([$this->getSource(), $this->getDestination()]);

            $this->lockRepository->delete($this->getLock());

            $this->dispatcher->dispatch(BalanceLockRemoved::NAME,
                new BalanceLockRemoved($this->getLock()));

            return true;

        } finally {
            $this->releaseAccounts([$this->getSource(), $this->getDestination()]);
        }

        return false;
    }

}