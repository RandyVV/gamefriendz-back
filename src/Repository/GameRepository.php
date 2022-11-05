<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function add(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Méthode de recherche par critères
    public function searchGames(array $criterias): array
    {
        $queryBuilder = $this->createQueryBuilder('g')
            ->select('g');

        // Recherche par titre de jeu
        if (key_exists('title', $criterias)) {
            $title = $criterias['title'];

            $queryBuilder->andWhere('g.title LIKE :title')
                ->setParameter('title', '%' . $title . '%');
        }

        // Recherche par plateforme
        if (key_exists('platform', $criterias)) {
            $platform = $criterias['platform'];

            $queryBuilder->join('g.releases', 'r')
                ->join('r.platform', 'p')
                ->andWhere('p.name LIKE :platform')
                ->setParameter('platform', '%' . $platform . '%');
        }

        return $queryBuilder->getQuery()
            ->getResult()
        ;
    
        // Sans critère :
        // SELECT * FROM game ORDER BY id ASC
        
        // Avec seulelement le critère "available"
        // SELECT * FROM game WHERE available = TRUE ORDER BY id ASC
    }
//    /**
//     * @return Game[] Returns an array of Game objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Game
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
