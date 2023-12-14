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

use App\Entity\Concurso;
use App\Entity\Loteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Concurso>
 *
 * @method Concurso|null find($id, $lockMode = null, $lockVersion = null)
 * @method Concurso|null findOneBy(array $criteria, array $orderBy = null)
 * @method Concurso[]    findAll()
 * @method Concurso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConcursoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Concurso::class);
    }

    public function findByLoteriaAndNumero(Loteria $loteria, int $concursoNumero): ?Concurso
    {
        return $this->createQueryBuilder('c')
                        ->select('c,l')
                        ->andWhere('c.loteria = :loteria')
                        ->setParameter('loteria', $loteria->getId()->toBinary())
                        ->andWhere('c.numero = :numero')
                        ->setParameter('numero', $concursoNumero)
                        ->innerJoin('c.loteria', 'l', Join::WITH, 'c.loteria = l.id')
                        ->getQuery()
                        ->getOneOrNullResult()
        ;
    }

    public function save(Concurso $concurso, bool $flush = false): void
    {
        $this->getEntityManager()->persist($concurso);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function listPesquisar(
        Loteria $loteria = null,
        int $firstResult = 1,
        int $maxResult = 10
    ): Paginator {
        if (!\in_array($maxResult, [10, 25, 50, 100], true)) {
            $maxResult = 10;
        }

        $query = $this->createQueryBuilder('c')
                ->select('l,c')
        ;

        if (null !== $loteria) {
            $query->andWhere('c.loteria = :loteria')
                    ->setParameter('loteria', $loteria->getId()->toBinary())
            ;
        }

        $query->innerJoin('c.loteria', 'l')
                ->andWhere('l.id = c.loteria')
                ->setFirstResult($firstResult)
                ->setMaxResults($maxResult)
                ->addOrderBy('l.nome', 'ASC')
                ->addOrderBy('c.numero', 'DESC')
                ->getQuery()
        ;

        return new Paginator($query);
    }

    public function findLast(Loteria $loteria): ?Concurso
    {
        return $this->createQueryBuilder('c')
                        ->where('c.loteria = :loteria')
                        ->setParameter('loteria', $loteria->getId()->toBinary())
                        ->andWhere('c.dezena IS NOT NULL')
                        ->orderBy('c.numero', 'DESC')
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return Concurso[] Returns an array of Concurso objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
    //    public function findOneBySomeField($value): ?Concurso
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
