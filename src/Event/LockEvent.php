<?php

namespace App\Event;

use App\Entity\Lock;
use Symfony\Component\EventDispatcher\Event;

class LockEvent extends Event implements LockEventInterface
{
    private $locks;


    /**
     * LockEvent constructor.
     *
     * @param array|Lock[] $locks
     */
    public function __construct(array $locks)
    {
        $this->locks = $locks;
    }


    /**
     * @return array|Lock[]
     */
    public function getLocks(): array
    {
        return $this->locks;
    }
}