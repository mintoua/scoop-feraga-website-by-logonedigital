<?php

namespace App\Repository;

use App\Entity\Likes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Likes>
 *
 * @method Likes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Likes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Likes[]    findAll()
 * @method Likes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Likes::class);
    }

    public function add(Likes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Likes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Likes[] Returns an array of Likes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

 public function isLiked($postId , $userId)
   {        return $this->createQueryBuilder('l')
       ->join('l.post','p')
       ->join('l.user','u')
       ->andWhere('p.id = :val')
       ->andWhere('u.id = :val2')
       ->setParameter('val', $postId)
       ->setParameter('val2', $userId)
           ->getQuery()
           ->getResult()
       ;
   }
    public function likesParPost($postId)
    {        return $this->createQueryBuilder('l')
        ->select("COUNT(l)")
        ->join('l.post','p')
        ->andWhere('p.id = :val')
        ->setParameter('val', $postId)
        ->getQuery()
        ->getSingleScalarResult()
        ;
    }
}
