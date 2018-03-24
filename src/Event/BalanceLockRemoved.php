<?php

namespace App\Event;

class BalanceLockRemoved extends LockEvent
{
    const NAME = 'balance.lock.removed';
}