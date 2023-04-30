<?php

namespace App\Repository;

use App\Entity\Player;
use App\Entity\Conversation;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Conversation>
 *
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    public function add(Conversation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Conversation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getOrCreateConversation(Player $player1, Player $player2): Conversation
    {
        $conversation = $this->createQueryBuilder('c')
            ->innerJoin('c.participate', 'p1')
            ->innerJoin('c.participate', 'p2')
            ->where('p1.id = :player1 AND p2.id = :player2')
            ->orWhere('p1.id = :player2 AND p2.id = :player1')
            ->setParameters([
                'player1' => $player1->getId(),
                'player2' => $player2->getId(),
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$conversation) {
            $conversation = new Conversation();
            $conversation->addParticipate($player1);
            $conversation->addParticipate($player2);
            $this->_em->persist($conversation);
            $this->_em->flush();
        }

        return $conversation;
    }

    public function findByPlayer(Player $player)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.participate', 'p')
            ->where('p.id = :player')
            ->setParameter('player', $player->getId())
            ->getQuery()
            ->getResult();
    }

    public function findByPlayers(Player $playerA, Player $playerB)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.participate', 'pa')
            ->innerJoin('c.participate', 'pb')
            ->where('pa.id = :playerA')
            ->andWhere('pb.id = :playerB')
            ->setParameter('playerA', $playerA->getId())
            ->setParameter('playerB', $playerB->getId())
            ->getQuery()
            ->getOneOrNullResult();
    }




    //    /**
    //     * @return Conversation[] Returns an array of Conversation objects
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

    //    public function findOneBySomeField($value): ?Conversation
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
