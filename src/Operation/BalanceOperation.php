<?php

namespace App\Operation;

use App\Entity\Account;
use App\Exception\AccountIsBusyException;
use App\Exception\InvalidOperationParameterException;
use App\Exception\NotEnoughBalanceException;
use App\Repository\AccountRepository;
use App\Repository\OperationRepository;
use PhpParser\Node\Expr\Closure;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class BalanceOperation implements BalanceOperationInterface
{
    protected $lockId = 0;
    protected $params;
    protected $dispatcher;
    protected $accountRepository;
    protected $operationRepository;


    /**
     * @return array|Closure[]
     */
    abstract protected function getValidators();


    /**
     * @return bool
     */
    abstract public function execute(): bool;


    /**
     * BalanceOperation constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param AccountRepository        $accountRepository
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        AccountRepository $accountRepository,
        OperationRepository $operationRepository
    ) {
        $this->dispatcher = $dispatcher;
        $this->accountRepository = $accountRepository;
        $this->operationRepository = $operationRepository;
    }


    /**
     * @param array $values
     *
     * @return BalanceOperationInterface
     * @throws InvalidOperationParameterException
     */
    public function bindParams(array $values): BalanceOperationInterface
    {
        $this->params = [];

        foreach ($this->getValidators() as $name => $validator) {
            if (isset($values[$name])) {
                $this->params[$name] = $validator($values[$name]);
            } else {
                throw new InvalidOperationParameterException("Parameter {$name} required.");
            }
        }

        return $this;
    }


    /**
     * @param int $value
     *
     * @return BalanceOperationInterface
     */
    public function bindLockId(int $value): BalanceOperationInterface
    {
        $this->lockId = $value;

        return $this;
    }


    /**
     * @param Account $account
     * @param int     $requiredAmount
     *
     * @throws NotEnoughBalanceException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function validateAvailableBalance(Account $account, int $requiredAmount): void
    {
        $available = $this->accountRepository->getAccountAvailableBalance($account);

        if ($requiredAmount > $available) {
            throw new NotEnoughBalanceException(sprintf('Account: %d, Required: %d, Available: %d',
                $account->getId(), $requiredAmount, $available));
        }
    }


    /**
     * @param Account $account
     *
     * @return bool
     * @throws AccountIsBusyException
     */
    protected function lockAccount(Account $account): bool
    {
        $locked = $this->accountRepository->lockAccount($account, $this->lockId);

        if (!$locked) {
            throw new AccountIsBusyException("Account ID: " . $account->getId());
        }

        return $locked;
    }


    /**
     * @param Account $account
     *
     * @return bool
     */
    protected function releaseAccount(Account $account): bool
    {
        return $this->accountRepository->releaseAccount($account, $this->lockId);
    }
}