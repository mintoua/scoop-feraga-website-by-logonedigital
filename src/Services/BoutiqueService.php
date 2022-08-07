<?php

namespace App\Services;

use App\Entity\Comments;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Cache\CacheInterface;

class BoutiqueService
{

    private $session;
    private $entityManager;
    private $paginator;
    private $cache;

    public function __construct(SessionInterface $session, EntityManagerInterface $entityManager, CacheInterface $cache , PaginatorInterface $paginator)
    {
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
        $this->cache = $cache;
        $this->session = $session;
    }

    public function addOrderSession($carrier,$delivery){
        $this->session->set  ('orderAdded',[$carrier,$delivery]);
    }

    public function getOrderSession(){
        return $this->session->get('orderAdded');
    }

    public function clearOrderSession(){
        return $this->session->remove('orderAdded');
    }

    public function persistComment($message,$rating,User $user, $product){
        $comment = new Comments();

        $comment->setProduct($product);
        $comment->setMessage($message);
        $comment->setRating((int)$rating);
        $comment->setCreatedAt(new \DateTime('now'));
        $comment->setAutor($user->getFirstname());
        $comment->setEmail($user->getUserIdentifier());
        $comment->setIsPublished(1);

        $this->entityManager->persist($comment);
        $this->cache->delete('product_reviews_list');
        $this->entityManager->flush();
    }

    public function toPaginate($item,$request,$limit){
        return $this->paginator->paginate(
            $item,
            $request->query->getInt('page',1),
            $limit
        );
    }
}