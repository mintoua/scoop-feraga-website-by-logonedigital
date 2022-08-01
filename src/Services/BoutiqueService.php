<?php

namespace App\Services;

use App\Entity\Comments;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class BoutiqueService
{

    private $entityManager;
    private $paginator;

    public function __construct(EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
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