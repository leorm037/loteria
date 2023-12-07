<?php

namespace App\Repository;

use App\Entity\Bolao;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bolao>
 *
 * @method Bolao|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bolao|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bolao[]    findAll()
 * @method Bolao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BolaoRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Bolao::class);
    }

    public function list(Usuario $usuario) {
        return $this->createQueryBuilder('b')
                        ->andWhere('b.usuario = :usuario')
                        ->setParameter('usuario', $usuario->getId()->toBinary())
                        ->orderBy('b.nome', 'ASC')
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function save(Bolao $bolao, bool $flush = false): void {
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
