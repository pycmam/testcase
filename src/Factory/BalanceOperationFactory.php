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
use App\Operation\ApproveLockOperation;
use App\Operation\BalanceOperationInterface;
use App\Operation\RemoveLockOperation;
use App\Operation\SubBalanceOperation;
use App\Operation\SubLockedBalanceOperation;
use App\Operation\TransferBalanceOperation;
use App\Operation\TransferLockedBalanceOperation;
use App\Repository\AccountRepository;
use App\Repository\LockRepository;
use App\Repository\OperationRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BalanceOperationFactory
{
    const OP_ADD      = 'add';
    const OP_SUB      = 'sub';
    const OP_TRANSFER = 'transfer';

    protected $dispatcher;
    protected $accountRepository;
    protected $operationRepository;
    protected $lockRepository;


    /**
     * BalanceOperationFactory constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param AccountRepository        $accountRepository
     * @param OperationRepository      $operationRepository
     * @param LockRepository           $lockRepository
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        AccountRepository $accountRepository,
        OperationRepository $operationRepository,
        LockRepository $lockRepository
    ) {
        $this->dispatcher = $dispatcher;
        $this->accountRepository = $accountRepository;
        $this->operationRepository = $operationRepository;
        $this->lockRepository = $lockRepository;
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
            'ApproveLock'    => ApproveLockOperation::class,
            'RemoveLock'     => RemoveLockOperation::class,
        ];

        if (isset($map[$type])) {

            return new $map[$type]($this->dispatcher, $this->accountRepository,
                $this->operationRepository, $this->lockRepository);
        } else {
            throw new UnsupportedOperationException("Operation {$type} not implemented.");
        }
    }

}