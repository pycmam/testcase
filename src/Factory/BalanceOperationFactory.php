<?php
/**
 * Created by PhpStorm.
 * User: rustam
 * Date: 23.03.2018
 * Time: 9:20
 */

namespace App\Factory;

use App\Exception\UnsupportedOperationException;
use App\Operation\AddBalanceOperation;
use App\Operation\AddLockedBalanceOperation;
use App\Operation\BalanceOperationInterface;
use App\Operation\SubBalanceOperation;
use App\Operation\SubLockedBalanceOperation;
use App\Operation\TransferBalanceOperation;
use App\Operation\TransferLockedBalanceOperation;
use Psr\Container\ContainerInterface;

class BalanceOperationFactory
{
    const OP_ADD      = 'add';
    const OP_SUB      = 'sub';
    const OP_TRANSFER = 'transfer';

    protected $container;


    /**
     * BalanceOperationFactory constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function create(string $type): BalanceOperationInterface
    {
        $map = [
            'Add'            => AddBalanceOperation::class,
            'AddLocked'      => AddLockedBalanceOperation::class,
            'Sub'            => SubBalanceOperation::class,
            'SubLocked'      => SubLockedBalanceOperation::class,
            'Transfer'       => TransferBalanceOperation::class,
            'TransferLocked' => TransferLockedBalanceOperation::class,
        ];

        if (isset($map[$type]))
        {
            return $this->container->get($map[$type]);
        } else {
            throw new UnsupportedOperationException("Operation {$type} not implemented.");
        }

    }

}