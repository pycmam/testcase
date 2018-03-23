<?php

namespace App\Event;

use App\Entity\Operation;
use Symfony\Component\EventDispatcher\Event;

class OperationEvent extends Event
{
    private $operations;


    /**
     * OperationEvent constructor.
     *
     * @param array|Operation[] $operations
     */
    public function __construct(array $operations)
    {
        $this->operations = $operations;
    }


    /**
     * @return Operation
     */
    public function getOperations(): array
    {
        return $this->operations;
    }
}