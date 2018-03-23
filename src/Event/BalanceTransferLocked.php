<?php

namespace App\Event;

class BalanceTransferLocked extends LockEvent
{
    const NAME = 'balance.transfer.locked';
}