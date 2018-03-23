<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Lock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Lock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lock[]    findAll()
 * @method Lock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LockRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Lock::class);
    }


    /**
     * @param Lock $lock
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function store(Lock $lock)
    {
        $this->getEntityManager()->persist($lock);
        $this->getEntityManager()->flush();
    }


    /**
     * @param Account $source
     * @param Account $destination
     * @param int     $amount
     *
     * @return Lock
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Account $source, Account $destination, int $amount)
    {
        $lock = new Lock();
        $lock->setSource($source);
        $lock->setDestination($destination);
        $lock->setAmount($amount);
        $lock->setCreated(new \DateTime());

        $this->store($lock);

        return $lock;
    }


    /**
     * @param Account $account
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOutAmountSum(Account $account): int
    {
        $result = $this->createQueryBuilder('o')
            ->select('SUM(o.amount) as amountSum')
            ->where('o.source = :source AND o.approved IS NULL')
            ->setParameter('source', $account->getId())
            ->getQuery()
            ->getSingleScalarResult();

        return (int)$result;
    }


    /**
     * @param Account $account
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getInAmountSum(Account $account): int
    {
        $result = $this->createQueryBuilder('o')
            ->select('SUM(o.amount) as amountSum')
            ->where('o.destination = :destination AND o.approved IS NULL')
            ->setParameter('destination', $account->getId())
            ->getQuery()
            ->getSingleScalarResult();

        return (int)$result;
    }

//    /**
//     * @return Lock[] Returns an array of Lock objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Lock
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
