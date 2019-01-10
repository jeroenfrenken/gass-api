<?php

namespace App\Repository;

use App\Entity\ActionRegister;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ActionRegister|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActionRegister|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActionRegister[]    findAll()
 * @method ActionRegister[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActionRegisterRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ActionRegister::class);
    }

    /**
     * @param string $identifier
     * @param \DateTime $time
     * @param string $action
     * @return ActionRegister[] Returns an array of ActionRegister objects
     */
    public function findByIdentifierAndHigherTimeAndAction(string $identifier, \DateTime $time, string $action)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.identifier = :identifier')
            ->andWhere('a.date > :time')
            ->andWhere('a.action = :action')
            ->setParameter('identifier', $identifier)
            ->setParameter('time', $time)
            ->setParameter('action', $action)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(15)
            ->getQuery()
            ->getResult()
        ;
    }

}
