<?php

namespace App\Repository;

use App\Entity\Topic;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Topic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Topic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Topic[]    findAll()
 * @method Topic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    // /**
    //  * @return Topic[] Returns an array of Topic objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Topic
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getTopicsData($page) {
        /*return $this->createQueryBuilder('t')
            ->select('t.id,t.name,t.author,u.id,u.username,u.roles')
            ->join('t.author', 'u')

            //->join('t.author', 'u', Join::ON, 'u.id = t.author')
            ->andWhere('t.visible = 1')
            ->getQuery()
            ->getResult()
        ;*/

        $start = 10 * ($page - 1);
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT t.id,t.name,t.author,t.complete,t.readOnly,t.staffOnly,u.username,u.roles FROM App\Entity\Topic t JOIN App\Entity\User u WITH t.author = u.id WHERE t.visible = 1")
                    ->setFirstResult( $start )
                    ->setMaxResults( 10 );
        return $query->getResult();
    }

    public function countPage() {
        $query = $this->createQueryBuilder('t')
        ->select('count(t.id)')
        ->where('t.visible = :visible')
        ->setParameter('visible', 1)
        ->getQuery();

        return ceil(($query->getSingleScalarResult())/10);
    }
}
