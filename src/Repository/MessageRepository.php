<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    // /**
    //  * @return Message[] Returns an array of Message objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getTopicData($topic) {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT t.id, t.name FROM App\Entity\Message m JOIN App\Entity\Topic t WITH m.idTopic = t.id WHERE m.id = '.$topic);
        return $query->execute();
    }

    public function getCountMessage($topic) {
        return $this->createQueryBuilder('m')
            ->select('count(m.id)')
            ->andWhere('m.idTopic = :id')
            ->setParameter('id', $topic)
            ->getQuery()
            ->getSingleScalarResult()
        ; 
    }

    public function getLastMessage($topic) {
        return $this->createQueryBuilder('m')
            ->select('m.publicationDate')
            ->andWhere('m.idTopic = :id')
            ->setParameter('id', $topic)
            ->orderBy('m.publicationDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult()
        ; 
    }

    public function getMessages($id) {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT m.id,m.idUser,m.publicationDate,m.content,m.visible,m.edited,u.username,u.roles,u.signature FROM App\Entity\Message m JOIN App\Entity\User u WITH m.idUser = u.id');
        return $query->getResult();
    }
}
