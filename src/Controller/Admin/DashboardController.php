<?php

namespace App\Controller\Admin;


use App\Entity\Comments;
use App\Entity\PostCategory;
use App\Entity\Posts;
use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\Carrier;
use App\Entity\Order;
use App\Entity\User;
use App\Entity\Contact;
use App\Entity\FarmPictures;
use App\Entity\CategoryPicture;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
      //  return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        $user = $this->getUser();
        // if(!$user->isBlocked){
        //     return $this->render('admin/index.html.twig');
        // }
        
        return $this->render('admin/index.html.twig');;
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="https://logonedigital.com/assetF/img/logo.png" />')
            ->setFaviconPath('/public/frontOffice/img/favicon_scoops_feraga.png');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fas fa-tachometer-alt-average');
        yield MenuItem::linkToDashboard('Stastistiques', 'fas fa-chart-bar');

        
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class)->setPermission('ROLE_SUPER_ADMIN');

        yield MenuItem::section('Boutique');
        yield MenuItem::linkToCrud('Categorie des Produits', 'fas fa-list', ProductCategory::class);
        yield MenuItem::linkToCrud('Produits', 'fas fa-store', Product::class);
        yield MenuItem::linkToCrud('Avis sur les produits', 'fas fa-comment', Comments::class);
        yield MenuItem::linkToCrud('Transporteurs', 'fas fa-truck', Carrier::class);
        yield MenuItem::linkToCrud('Commandes', 'fas fa-shopping-cart', Order::class);
        yield MenuItem::section('Blog');
        yield MenuItem::linkToCrud('Thématique', 'fas fa-list-alt', PostCategory::class);
        yield MenuItem::linkToCrud('Post', 'fas fa-newspaper', Posts::class);
        yield MenuItem::section('Demande de contacts');
        yield MenuItem::linkToCrud('Courriel', 'fas fa-envelope', Contact::class);
        yield MenuItem::section('Farm pictures');
        yield MenuItem::linkToCrud('Album photo', 'fas fa-images', CategoryPicture::class);
        yield MenuItem::linkToCrud('photo', 'fas fa-image', FarmPictures::class);
        
    }

    
}
