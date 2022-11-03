<?php

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Player>
 *
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    public function add(Player $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Player $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function searchPlayer(array $criterias): array
    {
       $queryBuilder = $this->createQueryBuilder('p')
           ->select('p')
           ->from(Player::class, 'p');
           
        if (key_exists('nickname', $criterias)) {
            $nickname = $criterias['nickname'];
            
            $queryBuilder->where('p.nickname LIKE = :nickname')
                ->setParameter('nickname', '%' . $nickname . '%');
        }
        
        if (key_exists('discord_tag', $criterias)) {
            $discordTag = $criterias['discord_tag'];
            
            $queryBuilder->where('p.discord_tag LIKE = :discord_tag')
                ->setParameter('discord_tag', '%' . $discordTag . '%');
        }
        
        if (key_exists('available', $criterias)) {
            $available = $criterias['available'];
            
            $queryBuilder->where('p.available = :available')
                ->setParameter('available', boolval($available));
        }
        
        return $queryBuilder->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        
        // Sans critère :
        // SELECT * FROM player ORDER BY id ASC
        
        // Avec seulement le critère "available"
        // SELECT * FROM player WHERE available = TRUE ORDER BY id ASC
    }
    
//    /**
//     * @return Player[] Returns an array of Player objects
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

//    public function findOneBySomeField($value): ?Player
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
