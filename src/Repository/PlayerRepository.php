<?php

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Player>
 *
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
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

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $player, string $newHashedPassword): void
    {
        if (!$player instanceof Player) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($player)));
        }

        $player->setPassword($newHashedPassword);

        $this->add($player, true);
    }
    // Méthode pour ajouter des critères de recherche
    public function searchPlayer(array $criterias): array
    {
       $queryBuilder = $this->createQueryBuilder('p')
           ->select('p')
           ->from(Player::class, 'p');

        // Critère de recherche par le pseudo
        if (key_exists('nickname', $criterias)) {
            $nickname = $criterias['nickname'];
            
            $queryBuilder->where('p.nickname LIKE = :nickname')
                ->setParameter('nickname', '%' . $nickname . '%');
        }
        
        // Critère de recherche par le discord_tag
        if (key_exists('discord_tag', $criterias)) {
            $discordTag = $criterias['discord_tag'];
            
            $queryBuilder->where('p.discord_tag LIKE = :discord_tag')
                ->setParameter('discord_tag', '%' . $discordTag . '%');
        }
        // Recherche par joueur connecté ou non connecté
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
