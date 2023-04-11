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
    /**
     * Liste des méthode qui permettent de configurer le query
     * builder par critère de recherche textuel
     */
    private static $textSearchCriteriasConfigure = [
        'nickname' => 'configureNicknameSearchCriteria',
        'discord_tag' => 'configureDiscordTagSearchCriteria',
        'game' => 'configureGameSearchCriteria'
    ];

    /**
     * Liste des méthodes qui permettent de mettre en forme la
     * valeur des critères de recherche textuel
     * (par exemple passer de "poke" à "%poke%" pour un LIKE)
     */
    private static $searchCriteriasFormat = [
        'nickname' => 'formatCriteriaLike',
        'discord_tag' => 'formatCriteriaLike',
        'game' => 'formatCriteriaLike'
    ];

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

    public function searchPlayers(array $criterias): array
    {
        // on crée le query builder pour une requête personnalisée
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p');

        // si on a bien préparé les critères textuels
        if (isset($criterias['search']) && !empty($criterias['search'])) {
            // on ajoute les conditions à la requête en les liant par des OR
            $queryBuilder->andWhere('p.nickname LIKE :search OR p.discord_tag LIKE :search')
                // ...et on injecte les valeurs en paramètres de la requête DQL
                ->setParameter('search', '%' . $criterias['search'] . '%');
        }

        // recherche par disponibilité
        if (isset($criterias['available'])) {
            $queryBuilder->andWhere('p.available = :available')
                ->setParameter('available', $criterias['available']);
        }

        return $queryBuilder->getQuery()
            ->getResult();
    }




    private function configureNicknameSearchCriteria()
    {
        return 'p.nickname LIKE :nickname';
    }

    private function configureDiscordTagSearchCriteria()
    {
        return 'p.discord_tag LIKE :discord_tag';
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function configureGameSearchCriteria($queryBuilder)
    {
        // on utilise une sous-requête ici pour ne pas exclure les joueurs sans jeu
        // comme le ferait une jointure
        $subQueryBuilder = $this->createQueryBuilder('gsp')
            ->join('gsp.wants_to_play', 'gsgop')
            ->join('gsgop.game', 'gsg')
            ->where($queryBuilder->expr()->eq('gsp.id', 'p.id'))
            ->andWhere('gsg.title LIKE :game');

        return (string) $queryBuilder->expr()->exists($subQueryBuilder->getDQL());
    }

    private function formatCriteriaLike($value)
    {
        return '%' . $value . '%';
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
