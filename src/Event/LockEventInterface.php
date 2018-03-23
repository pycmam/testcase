<?php

namespace App\Event;

use App\Entity\Lock;

interface LockEventInterface
{
    public function getLock(): Lock;
}