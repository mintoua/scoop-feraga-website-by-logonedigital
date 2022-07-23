<?php

namespace App\Controller\Admin;


use App\Entity\PostCategory;
use App\Entity\Posts;
use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\Carrier;
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
        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SCOOPS FERAGA');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('ProductCategories', 'fas fa-user', ProductCategory::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Categorie des Produits', 'fas fa-list', ProductCategory::class);
       // yield MenuItem::linkToCrud('Produits', 'fas fa-store', Product::class);
        yield MenuItem::linkToCrud('Actualités', 'fas fa-newspaper', Posts::class);
        yield MenuItem::linkToCrud('Categorie des actualités', 'fas fa-list-alt', PostCategory::class);

        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        yield MenuItem::section('Boutique');
        yield MenuItem::linkToCrud('Categorie des Produits', 'fas fa-list', ProductCategory::class);
        yield MenuItem::linkToCrud('Produits', 'fas fa-store', Product::class);
        yield MenuItem::linkToCrud('Transporteur', 'fas fa-truck', Carrier::class);
        yield MenuItem::section('Farm pictures');
        yield MenuItem::linkToCrud('Type d\'image', 'fas fa-images', CategoryPicture::class);
        yield MenuItem::linkToCrud('Images', 'fas fa-image', FarmPictures::class);
        yield MenuItem::linkToCrud('Contacts', 'fas fa-address-book', Contact::class);
    }
}
