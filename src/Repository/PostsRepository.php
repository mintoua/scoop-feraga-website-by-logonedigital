<?php

namespace App\Repository;

use App\Entity\Posts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Posts>
 *
 * @method Posts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Posts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Posts[]    findAll()
 * @method Posts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Posts::class);
    }

    public function add(Posts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Posts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Posts[] Returns an array of Posts objects
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

   public function getPaginatedPosts($page , $limit , $cat = null)
    {
        $query = $this->createQueryBuilder('p');
            //filtre les posts
            if( !$cat ==null ){
                $query->andWhere("p.postCategory = :cat")
                    ->setParameter("cat",$cat);
            }

            $query->orderBy("p.createdAt","desc")
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)


        ;
            return $query->getQuery()->getResult();

    }public function getAllPostesOrdred()
{
    $query = $this->createQueryBuilder('p')
        ->join('p.postCategory','cat')
        ->orderBy("p.createdAt","desc")
    ;
    return $query->getQuery()->getResult();

}
    public function getRelatedPosts($catId,$postId)
    {
        $query = $this->createQueryBuilder('p')
            ->join('p.postCategory','cat')
            ->andWhere('cat.id = :val')
            ->andWhere('p.id != :val2')
            ->setParameter('val',$catId)
            ->setParameter('val2',$postId)
            ->orderBy("p.createdAt","desc")
            ->setMaxResults(3);
        ;
        return $query->getQuery()->getResult();

    }

    public function getTotalPosts($cat=null)
     {
        $query = $this->createQueryBuilder('p');
         $query->select("count(p)");
         //filtre les posts
         if( !$cat ==null ){
             $query->where("p.postCategory = :cat")
                 ->setParameter("cat",$cat);
         }

        ;
         return $query->getQuery()->getSingleScalarResult();
    }
}
