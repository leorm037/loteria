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

use App\Entity\Apostador;
use App\Entity\Bolao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Apostador>
 *
 * @method Apostador|null find($id, $lockMode = null, $lockVersion = null)
 * @method Apostador|null findOneBy(array $criteria, array $orderBy = null)
 * @method Apostador[]    findAll()
 * @method Apostador[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApostadorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Apostador::class);
    }

    public function save(Apostador $apostador, bool $flush = false): void
    {
        $this->getEntityManager()->persist($apostador);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function list(Bolao $bolao)
    {
        return $this->createQueryBuilder('a')
                        ->where('a.bolao = :bolao')
                        ->setParameter('bolao', $bolao->getId()->toBinary())
                        ->orderBy('a.nome', 'ASC')
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function listEmail(Bolao $bolao)
    {
        return $this->createQueryBuilder('a')
                        ->where('a.bolao = :bolao')
                        ->setParameter('bolao', $bolao->getId()->toBinary())
                        ->andWhere('a.email IS NOT NULL')
                        ->orderBy('a.nome', 'ASC')
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function findById(Uuid $id): Apostador
    {
        return $this->createQueryBuilder('a')
                        ->where('a.id = :id')
                        ->setParameter('id', $id->toBinary())
                        ->getQuery()
                        ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return Apostador[] Returns an array of Apostador objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
    //    public function findOneBySomeField($value): ?Apostador
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
