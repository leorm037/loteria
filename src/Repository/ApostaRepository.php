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
use App\Entity\Concurso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
                        ->orderBy('CAST(a.dezena AS CHAR)', 'ASC')
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

    public function listPesquisar(
        Bolao $bolao,
        int $firstResult = 1,
        int $maxResult = 10
    ): Paginator {
        $query = $this->createQueryBuilder('a')
                ->where('a.bolao = :bolao')
                ->setParameter('bolao', $bolao->getId()->toBinary())
                ->orderBy('CAST(a.dezena AS CHAR)', 'ASC')
                ->setMaxResults($maxResult)
                ->setFirstResult($firstResult)
                ->getQuery()
        ;

        return new Paginator($query);
    }

    public function findNaoApurado(Concurso $concurso)
    {
        return $this->createQueryBuilder('a')
                        ->where('a.concurso = :concurso')
                        ->setParameter('concurso', $concurso->getId()->toBinary())
                        ->andWhere('a.isConferido = false')
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function findNaoConferidoConcursoSorteado()
    {
        return $this->createQueryBuilder('a')
                        ->select('a,c,b')
                        ->where('a.isConferido = false')
                        ->andWhere('c.dezena IS NOT NULL')
                        ->innerJoin('a.concurso', 'c', Join::WITH, 'a.concurso = c.id')
                        ->innerJoin('c.loteria', 'l', Join::WITH, 'c.loteria = l.id')
                        ->leftJoin('a.bolao', 'b', Join::WITH, 'a.bolao = b.id')
                        ->getQuery()
                        ->getResult()
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
