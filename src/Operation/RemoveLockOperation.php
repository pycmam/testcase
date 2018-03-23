<?php

namespace App\Operation;

class RemoveLockOperation extends LockOperation
{

    public function execute(): bool
    {
        try {
            $this->lockAccounts([$this->getSource(), $this->getDestination()]);

            $this->lockRepository->delete($this->getLock());

            return true;

        } finally {
            $this->releaseAccounts([$this->getSource(), $this->getDestination()]);
        }

        return false;
    }

}