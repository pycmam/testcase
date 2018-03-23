<?php

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    private $operationRepository;
    private $lockRepository;


    public function __construct(
        RegistryInterface $registry,
        OperationRepository $operationRepository,
        LockRepository $lockRepository
    ) {
        parent::__construct($registry, Account::class);

        $this->operationRepository = $operationRepository;
        $this->lockRepository = $lockRepository;
    }


    /**
     * @param Account $account
     * @param int     $lockId
     *
     * @return bool
     */
    public function lockAccount(Account $account, int $lockId): bool
    {
        $result = $this->createQueryBuilder('a')
            ->update()
            ->set('a.busyByPid', $lockId)
            ->where('a.id = :id AND a.busyByPid IS NULL')
            ->getQuery()
            ->execute(['id' => $account->getId()]);

        return $result > 0;
    }


    /**
     * @param Account $account
     * @param int     $lockId
     *
     * @return bool
     */
    public function releaseAccount(Account $account, int $lockId): bool
    {
        $result = $this->createQueryBuilder('a')
            ->update()
            ->set('a.busyByPid', 'null')
            ->where('a.id = :id AND a.busyByPid = :lockId')
            ->getQuery()
            ->execute(['id' => $account->getId(), 'lockId' => $lockId]);

        return $result > 0;
    }


    /**
     * @param Account $account
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAccountAvailableBalance(Account $account)
    {
        return $this->getAccountTotalBalance($account) - $this->getAccountLockedOutBalance($account);
    }


    /**
     * @param Account $account
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAccountTotalBalance(Account $account)
    {
        return $this->operationRepository->getAmountSum($account);
    }


    /**
     * @param Account $account
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAccountLockedOutBalance(Account $account)
    {
        return $this->lockRepository->getOutAmountSum($account);
    }


    /**
     * @param Account $account
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAccountLockedInBalance(Account $account)
    {
        return $this->lockRepository->getInAmountSum($account);
    }

//    /**
//     * @return Account[] Returns an array of Account objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Account
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
