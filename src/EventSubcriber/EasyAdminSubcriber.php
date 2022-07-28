<?php

namespace App\EventSubcriber;

use App\Entity\Posts;
use App\Entity\Product;
use DateTimeImmutable;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class EasyAdminSubcriber implements EventSubscriberInterface
{
    private $appKernel;

    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
    }

    public static function getSubscribedEvents()
    {
        // TODO: Implement getSubscribedEvents() method.
        return[
            BeforeEntityPersistedEvent::class => ['setProductImage']
        ];
    }

    public function setProductImage(BeforeEntityPersistedEvent $event){

        $entity = $event->getEntityInstance();
        if($entity instanceof Posts){
            $now =new DateTimeImmutable('now');
            $entity->setCreatedAt($now);
        }

    }
}