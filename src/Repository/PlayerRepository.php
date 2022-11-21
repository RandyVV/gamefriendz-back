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
    
    // Méthode pour ajouter des critères de recherche
    public function searchPlayers(array $criterias): array
    {
        // on crée le query builder pour une requête personnalisée
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p');

        /* RECHERCHE TEXTUELLE */
        /*
        Tous les critères de recherche textuels se combinent :
            si on cherche avec un pseudo et un tag Discord
                => alors on recherche le pseudo OU le tag Discord
            si on cherche avec pseudo, tag Discord et jeu
                => alors on recherche le pseudo OU le tag Discord OU le titre du jeu

        Exemple :
            nickame => bg
            game => pokemon

            On obtiendra tous les joueurs qui on "bg" dans leur pseudo
            ou qui veulent jouer à un jeu dont le titre contient "pokemon"
        */

        // on mettra ici les différentes conditions en fonction de chaque critère textuel
        $textCriterias = [];
        // on mettra ici les valeurs mises en forme qui seront données comme paramètres au QB
        $textParameters = [];

        // recherche des critères textuels => pour chaque critère à configurer...
        foreach (self::$textSearchCriteriasConfigure as $criteria => $configMethod) {
            // ...si le critère n'a pas été donné, on le saute
            if (!key_exists($criteria, $criterias)) {
                continue;
            }
            // ...on génère la condition DQL qui gère le critère après avoir
            // configuré le query builder (jointures, etc.)
            $textCriterias[] = $this->$configMethod($queryBuilder);
            // s'il faut mettre en forme la valeur du critère pour la requête...
            // (ex: ajouter les % pour un LIKE)
            if (key_exists($criteria, self::$searchCriteriasFormat)) {
                // ...on récupère le nom de la méthode qui sert à formater la valeur
                $formatter = self::$searchCriteriasFormat[$criteria];
                // ...on appelle la méthode pour formater la valeur et on ajoute cette
                // valeur mise en forme aux critères qui seront passés au QB
                $textParameters[$criteria] = $this->$formatter($criterias[$criteria]);
            } else {
                // s'il ne faut pas formater la valeur on l'ajoute telle quelle aux
                // paramètres du QB
                $textParameters[$criteria] = $criterias[$criteria];
            }
        }

        // si on a bien préparé les critères textuels
        if (count($textCriterias) > 0) {
            // ...on ajoute les conditions à la requêtes en les liant par des OR
            $queryBuilder->andWhere(join(' OR ', $textCriterias))
                // ...et on injecte les valeurs en paramètres de la requête DQL
                ->setParameters($textParameters);
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
            ->andWhere('gsg.title LIKE :game')
        ;

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
