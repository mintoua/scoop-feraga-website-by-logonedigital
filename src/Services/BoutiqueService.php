<?php

namespace App\Services;

use App\Entity\Comments;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class BoutiqueService
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
}