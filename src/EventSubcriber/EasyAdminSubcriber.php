<?php

namespace App\EventSubcriber;

use App\Entity\Posts;
use App\Entity\Product;
use App\Entity\PostCategory;
use App\Entity\ProductCategory;
use DateTimeImmutable;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Cache\CacheInterface;

class EasyAdminSubcriber implements EventSubscriberInterface
{
    private $appKernel;
    private $cache;

    public function __construct(CacheInterface $cache, KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
        $this->cache = $cache;
    }

    public static function getSubscribedEvents()
    {
        // TODO: Implement getSubscribedEvents() method.
        return[
            BeforeEntityPersistedEvent::class => ['setCreatedAt'],
            BeforeEntityUpdatedEvent::class => ['setSlug'],
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
    public function setSlug(BeforeEntityUpdatedEvent $event){

        $entity = $event->getEntityInstance();
        if($entity instanceof Posts){
            $entity->setSlug($entity->getTitle());
        }
        if($entity instanceof PostCategory){
            $entity->setSlug($entity->getName());
        }
    }

    public function clearCacheAfter(AfterEntityPersistedEvent $event){

        $entity = $event->getEntityInstance();
        if($entity instanceof Posts){
            $this->cache->delete('post_list');
        }
        if($entity instanceof PostCategory){
            $this->cache->delete('category_list');
        }
        if($entity instanceof ProductCategory){
            $this->cache->delete('product_categories_list');
        }
        if($entity instanceof Product){
            $this->cache->delete('product_list');
        }
    }
    public function clearCacheAfterDeleted(AfterEntityDeletedEvent $event){
        $entity = $event->getEntityInstance();
        if($entity instanceof Posts){
            $this->cache->delete('post_list');
        }
        if($entity instanceof PostCategory){
            $this->cache->delete('category_list');
        }
        if($entity instanceof ProductCategory){
            $this->cache->delete('product_categories_list');
        }
        if($entity instanceof Product){
            $this->cache->delete('product_list');
        }
    }
    public function clearCacheAfterUpdated(AfterEntityUpdatedEvent $event){
        $entity = $event->getEntityInstance();
        if($entity instanceof Posts){
            $this->cache->delete('post_list');
        }
        if($entity instanceof PostCategory){
            $this->cache->delete('category_list');
        }
        if($entity instanceof ProductCategory){
            $this->cache->delete('product_categories_list');
        }
        if($entity instanceof Product){
            $this->cache->delete('product_list');
        }
    }
}