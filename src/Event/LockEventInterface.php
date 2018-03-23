<?php

namespace App\Event;

interface LockEventInterface
{
    public function getLocks(): array;
}