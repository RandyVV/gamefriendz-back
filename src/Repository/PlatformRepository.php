<?php

namespace App\Repository;

use App\Entity\Platform;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Platform>
 *
 * @method Platform|null find($id, $lockMode = null, $lockVersion = null)
 * @method Platform|null findOneBy(array $criteria, array $orderBy = null)
 * @method Platform[]    findAll()
 * @method Platform[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlatformRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Platform::class);
    }

    public function add(Platform $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Platform $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Récupère mes platformes par ordre alphabétique en bdd
     * 
     * @return Platform[]
     */
    public function findAllOrderByNameSearch($needle = null): array
       {
           return $this->createQueryBuilder('p')
                ->orderBy('p.name')
                ->where('p.name LIKE :needle')
                ->setParameter(':needle',"%".$needle."%")
                ->getQuery()
                ->getResult()
           ;
       }

       /**
     * Récupère mes plateformes de la plus récente à la plus ancienne
     * 
     * @return Movie[]
     */
    public function findAllOrderByReleaseDate(): array
    {
        return $this->createQueryBuilder('p')
             ->orderBy('p.release_date','DESC')
             ->setMaxResults(10)
             ->getQuery()
             ->getResult()
        ;
    } 

//    /**
//     * @return Platform[] Returns an array of Platform objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Platform
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
