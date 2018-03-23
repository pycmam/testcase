<?php

namespace App\Operation;

interface BalanceOperationInterface
{
    public function execute(): bool;


    public function bindParams(array $params): BalanceOperationInterface;


    public function bindLockId(int $value): BalanceOperationInterface;
}