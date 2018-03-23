<?php
/**
 * Created by PhpStorm.
 * User: rustam
 * Date: 22.03.2018
 * Time: 16:31
 */

namespace App\Service;

use App\Entity\Account;
use App\Entity\Operation;
use App\Repository\AccountRepository;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;

class UserBalanceService
{
    protected $entityManager;
    protected $accountRepository;
    protected $operationRepository;


    public function __construct(
        EntityManager $entityManager,
        AccountRepository $accountRepository,
        OperationRepository $operationRepository
    ) {
        $this->entityManager = $entityManager;
        $this->accountRepository = $accountRepository;
        $this->operationRepository = $operationRepository;
    }


    /**
     * Add amount to account balance
     *
     * @param Account $account
     * @param int     $amount
     * @param bool    $locked
     *
     * @return int
     * @throws \Doctrine\ORM\ORMException
     */
    public function add(Account $account, int $amount, bool $locked = false): Operation
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Amount must be greater than 0.");
        }

        $operation = new Operation();
        $operation->setAccount($account);
        $operation->setAmount($amount);
        $operation->setIsLocked($locked);

        $this->entityManager->persist($operation);

        return $operation;
    }


    /**
     * @param Account $account
     * @param int     $amount
     * @param bool    $locked
     *
     * @return int
     * @throws \Doctrine\ORM\ORMException
     */
    public function sub(Account $account, int $amount, bool $locked = false): Operation
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Amount must be greater than 0.");
        }

        $operation = new Operation();
        $operation->setAccount($account);
        $operation->setAmount($amount * -1);
        $operation->setIsLocked($locked);

        $this->entityManager->persist($operation);

        return $operation;
    }


    public function transfer(Account $source, Account $destination, int $amount, bool $locked = false): Operation
    {




    }


}