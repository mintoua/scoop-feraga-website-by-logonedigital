<?php

namespace App\EventSubcriber;

use App\Entity\CategoryPicture;
use App\Entity\Comments;
use App\Entity\FarmPictures;
use App\Entity\Posts;
use App\Entity\Product;
use App\Entity\PostCategory;
use App\Entity\ProductCategory;
use App\Entity\User;
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
            //BeforeEntityPersistedEvent::class => ['setCreatedAt'],
            //BeforeEntityUpdatedEvent::class => ['setSlug'],
            AfterEntityPersistedEvent::class => ['clearCacheAfter'],
            AfterEntityDeletedEvent::class => ['clearCacheAfterDeleted'],
            AfterEntityUpdatedEvent::class => ['clearCacheAfterUpdated'],
            BeforeEntityPersistedEvent::class=>['persistanceUserProcess'],
            BeforeEntityUpdatedEvent::class=>['updatedUserProcess']
        ];
    }

   /* public function setCreatedAt(BeforeEntityPersistedEvent $event){

        $entity = $event->getEntityInstance();
        if($entity instanceof Posts){
            $now =new DateTimeImmutable('now');
            $entity->setCreatedAt($now);
        }
    }*/

    /*
    public function setSlug(BeforeEntityUpdatedEvent $event){

        $entity = $event->getEntityInstance();
        if($entity instanceof Posts){
            $entity->setSlug($entity->getTitle());
        }
        if($entity instanceof PostCategory){
            $entity->setSlug($entity->getName());
        }

        if($entity instanceof ProductCategory)
        {
            $entity->setSlug($entity->getName());
        }

        if ($entity instanceof Product){
            $entity->setSlug($entity->getProduct_name());
        }
    }*/

    //permet de faire des actions sur l'utisateur lorsqu'il est ajouter depuis le dashboard
    public function persistanceUserProcess(BeforeEntityPersistedEvent $event){
        $entity = $event->getEntityInstance();
        if($entity instanceof User){
            $entity->setPassword(md5(uniqid()));
            $entity->setCreatedAt(new \DateTime('now'));
            //$entity->setBlocked(false);
        }
        //permet de set la date de creation a la current date lorsque l'admin add un post
        if($entity instanceof Posts){
            $now =new DateTimeImmutable('now');
            $entity->setCreatedAt($now);
        }
        //permet de set le slug = title lors de persistance du post
        if($entity instanceof Posts){
            $entity->setSlug($entity->getTitle());
        }
        //permet de set le slug = name lors de persistance du categoryPost
        if($entity instanceof PostCategory){
            $entity->setSlug($entity->getName());
        }


    }

    /**
     * permet de faire des actions après la modification d'un utilisateur
     *
     * @param BeforeEntityUpdatedEvent $event
     * @return void
     */
    public function updatedUserProcess(BeforeEntityUpdatedEvent $event){
        //  dd('hello world');
        $entity = $event->getEntityInstance();
        if($entity instanceof User){
            $entity->setUpdatedAt(new \DateTime('now'));
        }

        // modifier les slugs lors de la modification
        if($entity instanceof Posts){
            $entity->setSlug($entity->getTitle());
        }
        if($entity instanceof PostCategory){
            $entity->setSlug($entity->getName());
        }

        /*if($entity instanceof ProductCategory)
        {
            $entity->setSlug($entity->getName());
        }

        if ($entity instanceof Product){
            $entity->setSlug($entity->getProduct_name());
        }*/

    }

    public function clearCacheAfter(AfterEntityPersistedEvent $event){

        $entity = $event->getEntityInstance();
        if($entity instanceof Posts){
            $this->cache->delete('post_list');
        }
        if($entity instanceof PostCategory){
            $this->cache->delete('category_list');
            $this->cache->delete('post_list');
        }
        if($entity instanceof ProductCategory){
            $this->cache->delete('product_categories_list');
            $this->cache->delete('product_list');
            $this->cache->delete ('product_best_list');
        }
        if($entity instanceof Product){
            $this->cache->delete('product_list');
            $this->cache->delete ('product_best_list');
            $this->cache->delete('product_reviews_list');
        }

        if ($entity instanceof Comments){
            $this->cache->delete('product_reviews_list');
        }

        if($entity instanceof CategoryPicture){
            $this->cache->delete('farm-picture');
            $this->cache->delete('categeroriesPictureFarm');
        }
        if($entity instanceof FarmPictures){
            $this->cache->delete('farm-picture');
            $this->cache->delete('categeroriesPictureFarm');
        }
    }
    public function clearCacheAfterDeleted(AfterEntityDeletedEvent $event){
        $entity = $event->getEntityInstance();
        if($entity instanceof Posts){
            $this->cache->delete('post_list');
        }
        if($entity instanceof PostCategory){
            $this->cache->delete('category_list');
            $this->cache->delete('post_list');
        }
        if($entity instanceof ProductCategory){
            $this->cache->delete('product_categories_list');
            $this->cache->delete('product_list');
            $this->cache->delete ('product_best_list');
        }
        if($entity instanceof Product){
            $this->cache->delete('product_list');
            $this->cache->delete ('product_best_list');
            $this->cache->delete('product_reviews_list');
        }
        if ($entity instanceof Comments){
            $this->cache->delete('product_reviews_list');
        }
        if($entity instanceof CategoryPicture){
            $this->cache->delete('farm-picture');
            $this->cache->delete('categeroriesPictureFarm');
        }
        if($entity instanceof FarmPictures){
            $this->cache->delete('farm-picture');
            $this->cache->delete('categeroriesPictureFarm');
        }
    }

    public function clearCacheAfterUpdated(AfterEntityUpdatedEvent $event){
        $entity = $event->getEntityInstance();
        if($entity instanceof Posts){
            $this->cache->delete('post_list');
            $this->cache->delete('post_home');
        }
        if($entity instanceof PostCategory){
            $this->cache->delete('category_list');
            $this->cache->delete('post_list');
            $this->cache->delete('post_home');
        }
        if($entity instanceof ProductCategory){
            $this->cache->delete('product_categories_list');
            $this->cache->delete('product_list');
            $this->cache->delete ('product_best_list');
        }
        if($entity instanceof Product){
            $this->cache->delete('product_list');
            $this->cache->delete ('product_best_list');
            $this->cache->delete('product_reviews_list');
        }
        if ($entity instanceof Comments){
            $this->cache->delete('product_reviews_list');
        }
        if($entity instanceof CategoryPicture){
            $this->cache->delete('farm-picture');
            $this->cache->delete('categeroriesPictureFarm');
        }
        if($entity instanceof FarmPictures){
            $this->cache->delete('farm-picture');
            $this->cache->delete('categeroriesPictureFarm');
        }
    }
}