<?php

/*
 *     This file is part of Loteria.
 *
 *     (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 *     This source file is subject to the MIT license that is bundled
 *     with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Loteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Cache;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Loteria>
 *
 * @method Loteria|null find($id, $lockMode = null, $lockVersion = null)
 * @method Loteria|null findOneBy(array $criteria, array $orderBy = null)
 * @method Loteria[]    findAll()
 * @method Loteria[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoteriaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Loteria::class);
    }

    public function list()
    {
        return $this->createQueryBuilder('l')
                        ->orderBy('l.nome', 'ASC')
                        ->setCacheRegion('loteria_region')
                        ->setCacheMode(Cache::MODE_GET)
                        ->setCacheable(true)
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function findBySlug(string $loteriaSlug): ?Loteria
    {
        return $this->createQueryBuilder('l')
                        ->where('l.slug = :slug')
                        ->setParameter('slug', $loteriaSlug)
                        ->getQuery()
                        ->getOneOrNullResult()
        ;
    }

    public function findById(Uuid $id): ?Loteria
    {
        return $this->createQueryBuilder('l')
                        ->andWhere('l.id = :id')
                        ->setParameter('id', $id->toBinary())
                        ->setCacheable(true)
                        ->getQuery()
                        ->getOneOrNullResult()
        ;
    }

    public function save(Loteria $loteria, bool $flush = false): void
    {
        $this->getEntityManager()->persist($loteria);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Loteria[] Returns an array of Loteria objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
    //    public function findOneBySomeField($value): ?Loteria
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
