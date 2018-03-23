<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Operation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Operation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Operation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Operation[]    findAll()
 * @method Operation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Operation::class);
    }


    /**
     * @param Operation $operation
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function store(Operation $operation)
    {
        $this->getEntityManager()->persist($operation);
        $this->getEntityManager()->flush();
    }


    /**
     * @param Account $account
     * @param int     $amount
     *
     * @return Operation
     */
    public function create(Account $account, int $amount)
    {
        $operation = new Operation();
        $operation->setAccount($account);
        $operation->setAmount($amount);
        $operation->setCreated(new \DateTime());

        $this->store($operation);

        return $operation;
    }


    /**
     * @param callable $transaction
     *
     * @return bool|mixed
     * @throws \Throwable
     */
    public function transactional(callable $transaction)
    {
        return $this->getEntityManager()->transactional($transaction);
    }


    /**
     * @param Account $account
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAmountSum(Account $account): int
    {
        $result = $this->createQueryBuilder('o')
            ->select('SUM(o.amount) as amountSum')
            ->where('o.account = :account')
            ->setParameter('account', $account->getId())
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result;
    }


//    /**
//     * @return Operation[] Returns an array of Operation objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Operation
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
