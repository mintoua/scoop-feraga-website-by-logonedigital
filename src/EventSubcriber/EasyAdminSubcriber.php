<?php

namespace App\EventSubcriber;

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
        $tmp_name = $entity->getProductImage();
        $filename =uniqid();

        $extension = pathinfo($_FILES['Product']['name']['product_image']['file'], PATHINFO_EXTENSION,);

        $project_dir = $this->appKernel->getProjectDir();
        move_uploaded_file($tmp_name, $project_dir.'/public/uploads/images/'.$filename.'.'.$extension);

        $entity->setProductImage($filename.'.'.$extension);
       // dump($entity->getProductImage());
        //dd('here');
    }
}