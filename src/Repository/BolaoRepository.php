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

use App\Entity\Bolao;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Bolao>
 *
 * @method Bolao|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bolao|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bolao[]    findAll()
 * @method Bolao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BolaoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bolao::class);
    }

    public function list(Usuario $usuario)
    {
        return $this->createQueryBuilder('b')
                        ->select('b AS bolao,c,l')
                        ->addSelect('(SELECT SUM(ap1.quantidadeCota) FROM App\Entity\Apostador AS ap1 WHERE ap1.bolao = b.id) as total')
                        ->addSelect('(SELECT COUNT(ap2.id) FROM App\Entity\Apostador AS ap2 WHERE ap2.bolao = b.id) as apostadores')
                        ->addSelect('(SELECT COUNT(a1.id) FROM App\Entity\Aposta AS a1 WHERE a1.bolao = b.id) as apostas')
                        ->andWhere('b.usuario = :usuario')
                        ->setParameter('usuario', $usuario->getId()->toBinary())
                        ->innerJoin('b.concurso', 'c', Join::WITH, 'b.concurso = c.id')
                        ->innerJoin('c.loteria', 'l', Join::WITH, 'c.loteria = l.id')
                        ->addOrderBy('l.nome', 'ASC')
                        ->addOrderBy('c.numero', 'DESC')
                        ->addOrderBy('b.nome', 'ASC')
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function findById(Uuid $uuid, Usuario $usuario): ?Bolao
    {
        return $this->createQueryBuilder('b')
                        ->select('b,c,l')
                        ->andWhere('b.id = :id')
                        ->setParameter('id', $uuid->toBinary())
                        ->andWhere('b.usuario = :usuario')
                        ->setParameter('usuario', $usuario->getId()->toBinary())
                        ->innerJoin('b.concurso', 'c', Join::WITH, 'b.concurso = c.id')
                        ->innerJoin('c.loteria', 'l', Join::WITH, 'c.loteria = l.id')
                        ->orderBy('b.nome', 'ASC')
                        ->getQuery()
                        ->getOneOrNullResult()
        ;
    }

    public function findByUuid(Uuid $uuid): ?Bolao
    {
        return $this->createQueryBuilder('b')
                        ->select('b,c,l')
                        ->andWhere('b.id = :id')
                        ->setParameter('id', $uuid->toBinary())
                        ->innerJoin('b.concurso', 'c', Join::WITH, 'b.concurso = c.id')
                        ->innerJoin('c.loteria', 'l', Join::WITH, 'c.loteria = l.id')
                        ->orderBy('b.nome', 'ASC')
                        ->getQuery()
                        ->getOneOrNullResult()
        ;
    }

    public function save(Bolao $bolao, bool $flush = false): void
    {
        $this->getEntityManager()->persist($bolao);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Bolao[] Returns an array of Bolao objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
    //    public function findOneBySomeField($value): ?Bolao
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
