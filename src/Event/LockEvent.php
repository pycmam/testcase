<?php

namespace App\Event;

use App\Entity\Lock;
use Symfony\Component\EventDispatcher\Event;

class LockEvent extends Event implements LockEventInterface
{
    private $lock;


    /**
     * LockEvent constructor.
     *
     * @param Lock $lock
     */
    public function __construct(Lock $lock)
    {
        $this->lock = $lock;
    }


    /**
     * @return Lock
     */
    public function getLock(): Lock
    {
        return $this->lock;
    }
}