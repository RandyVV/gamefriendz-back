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
    public function searchPlayers(array $criterias): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p');

        /* RECHERCHE PAR PSEUDO, TAG DISCORD */
        $byNickname = false;
        $nickname = null;
        if (key_exists('nickname', $criterias)) {
            $byNickname = true;
            $nickname = $criterias['nickname'];
        }
        
        $byDiscordTag = false;
        $discordTag = null;
        if (key_exists('discord_tag', $criterias)) {
            $byDiscordTag = true;
            $discordTag = $criterias['discord_tag'];
        }

        /*
        Players :
            1 [pseudo => tartampion, discord => abc]
            2 [pseudo => autrechose, discord => bcd]
            3 [pseudo => tartatin, discord => xyz]

        Recherche [pseudo => tar] => 1,3
        Recherche [discord => bc] => 1,2
        Recherche [pseudo => tar, discord => bc] => 1,2,3
        */

        if ($byNickname && $byDiscordTag) {
            // recherche par pseudo ET tag discord
            //  => on prend les joueurs pour lesquels le pseudo OU le tag discord contiennent le terme de recherche
            $queryBuilder->andWhere('p.nickname LIKE :nickname OR p.discord_tag LIKE :discord_tag')
                ->setParameters([
                    'nickname' => '%' . $nickname . '%',
                    'discord_tag' => '%' . $discordTag . '%'
                ]);
        }
        elseif ($byNickname) {
            // recherche par pseudo (SANS tag discord)
            //  => on prend les joueurs dont SEUL le pseudo contient le terme de recherche
            $queryBuilder->andWhere('p.nickname LIKE :nickname')
                ->setParameter('nickname', '%' . $nickname . '%');
        }
        elseif ($byDiscordTag) {
            // recherche par tag discord (SANS pseudo)
            //  => on prend les joueurs dont SEUL le tag discord contient le terme de recherche
            $queryBuilder->andWhere('p.discord_tag LIKE :discord_tag')
                ->setParameter('discord_tag', '%' . $discordTag . '%');
        }

        /* RECHERCHE PAR DISPONIBILITÉ */
        if (key_exists('available', $criterias)) {
            $available = $criterias['available'];
            
            $queryBuilder->andWhere('p.available = :available')
                ->setParameter('available', boolval($available));
        }

        return $queryBuilder->getQuery()
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
