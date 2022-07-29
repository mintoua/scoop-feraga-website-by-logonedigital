<?php

namespace App\EventSubcriber;

use App\Entity\Posts;
use App\Entity\Product;
use App\Entity\PostCategory;
use DateTimeImmutable;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Cache\CacheInterface;

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
            BeforeEntityPersistedEvent::class => ['setCreatedAt'],
            AfterEntityPersistedEvent::class => ['clearCacheAfter'],
            AfterEntityDeletedEvent::class => ['clearCacheAfterDeleted'],
            AfterEntityUpdatedEvent::class => ['clearCacheAfterUpdated']
        ];
    }

    public function setCreatedAt(BeforeEntityPersistedEvent $event){

        $entity = $event->getEntityInstance();
        if($entity instanceof Posts){
            $now =new DateTimeImmutable('now');
            $entity->setCreatedAt($now);
        }
    }
    public function clearCacheAfter( CacheInterface $cache,AfterEntityPersistedEvent $event){

        $entity = $event->getEntityInstance();
        if($entity instanceof Posts){
            $cache->delete('post_list');
            $cache->delete('total_post');
        }
        if($entity instanceof PostCategory){
            $cache->delete('category_list');
        }
    }
    public function clearCacheAfterDeleted( CacheInterface $cache,AfterEntityDeletedEvent $event){
        $entity = $event->getEntityInstance();
        if($entity instanceof Posts){
            $cache->delete('post_list');
            $cache->delete('total_post');
        }
        if($entity instanceof PostCategory){
            $cache->delete('category_list');
        }
    }public function clearCacheAfterUpdated( CacheInterface $cache,AfterEntityUpdatedEvent $event){
        $entity = $event->getEntityInstance();
        if($entity instanceof Posts){
            $cache->delete('post_list');
        }
        if($entity instanceof PostCategory){
            $cache->delete('category_list');
        }
    }
}