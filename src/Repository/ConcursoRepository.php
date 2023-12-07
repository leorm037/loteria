<?php

namespace App\Repository;

use App\Entity\Concurso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
