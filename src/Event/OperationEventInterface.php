<?php

namespace App\Event;

interface OperationEventInterface
{
    public function getOperations(): array;
}