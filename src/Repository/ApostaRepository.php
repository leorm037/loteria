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

use App\Entity\Aposta;
use App\Entity\Bolao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Aposta>
 *
 * @method Aposta|null find($id, $lockMode = null, $lockVersion = null)
 * @method Aposta|null findOneBy(array $criteria, array $orderBy = null)
 * @method Aposta[]    findAll()
 * @method Aposta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApostaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Aposta::class);
    }

    public function save(Aposta $aposta, bool $flush = false): void
    {
        $this->getEntityManager()->persist($aposta);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByBolao(Bolao $bolao)
    {
        return $this->createQueryBuilder('a')
                        ->where('a.bolao = :bolao')
                        ->setParameter('bolao', $bolao->getId()->toBinary())
                        ->orderBy('a.dezena', 'ASC')
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function findByDezenasOnBolao(Aposta $aposta): Aposta|null
    {
        return $this->createQueryBuilder('a')
                        ->andwhere('a.bolao = :bolao')
                        ->setParameter('bolao', $aposta->getBolao()->getId()->toBinary())
                        ->andWhere('json_contains(a.dezena, :dezena) = 1')
                        ->setParameter('dezena', json_encode($aposta->getDezena()))
                        ->getQuery()
                        ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return Aposta[] Returns an array of Aposta objects
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
    //    public function findOneBySomeField($value): ?Aposta
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
