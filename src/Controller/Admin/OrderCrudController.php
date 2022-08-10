<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $adminUrlGenerator;

    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $generator)
    {
        $this->entityManager = $entityManager;
        $this->adminUrlGenerator = $generator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function deleteEntity ( EntityManagerInterface $entityManager , $entityInstance ) : void
    {
        foreach ($entityInstance->getOrderDetails() as $product ){
            $entityManager->remove ($product);
        }
        $entityManager->remove ($entityInstance);
        $entityManager->flush ();
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation','Préparation en cours','fas fa-box-open')
            ->linkToCrudAction('updatePreparation');

        $updateDelivering = Action::new('updateDelivering','Livraison en cours','fas fa-truck')
            ->linkToCrudAction('updateDelivering');

        $delivered = Action::new('delivered','Commande Livrée','fas fa-bag-shopping')
            ->linkToCrudAction('delivered');

        $generatePdf = Action::new ('generatePdf', 'Générer le Bon de Commande', 'fas fa-file-pdf')
            ->linkToCrudAction ('generatePdf');

       return $actions
           ->add('index','detail')
           ->add('detail', $updatePreparation)
           ->add('detail', $updateDelivering)
           ->add('detail',$delivered)
           ->add ('detail',$generatePdf)
           ->disable(Action::NEW)
           ->add(Crud::PAGE_EDIT, Action::INDEX);
    }

    public function urlDispatcher(String $action){
        $url = $this->adminUrlGenerator
            ->setDashboard(DashboardController::class)
            ->setController(OrderCrudController::class)
            ->setAction($action)
            ->generateUrl();

        return $url;
    }

    public function generatePdf(AdminContext $context){

        $order = $context->getEntity ()->getInstance ();
        if($order){
         return $this->redirectToRoute ('generate_pdf',['id'=>$order->getId()]);
        }
        return $this->redirect($this->urlDispatcher (Action::INDEX));
    }

    public function delivered(AdminContext $context){

        $order = $context->getEntity()->getInstance();
        foreach ($order->getOrderDetails() as $productInCart ){
           $product = $this->entityManager->getRepository (Product::class)->findOneByProductName($productInCart->getProduct());

           $product->setProductQuantity($product->getProductQuantity() - $productInCart->getQuantity());
           if($product->getProductQuantity() == 0 ){
               $this->addFlash ('notice', "<span style='color: red;'> <strong>Le produit ".$product->getProductName().": Quantité en Stock = 0</strong> </span>");
           }
        }

        $order->setState(4);
        $this->entityManager->flush();
        $this->addFlash('notice',"<span style='color: green'  > <strong> La commande ".$order->getReference()." a bien été mise à jour  </strong> </span>");
        return $this->redirect($this->urlDispatcher (Action::INDEX));
    }

    public function updatePreparation(AdminContext $context){
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->entityManager->flush();
        $this->addFlash('notice',"<span style='color: green'  > <strong> La commande ".$order->getReference()." a bien été mise à jour  </strong> </span>");
        return $this->redirect($this->urlDispatcher (Action::INDEX));
    }

    public function updateDelivering(AdminContext $context){
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->entityManager->flush();
        $this->addFlash('notice',"<span style='color: orange'  > <strong> La commande ".$order->getReference()." a bien été mise à jour  </strong> </span>");
        return $this->redirect($this->urlDispatcher (Action::INDEX));
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gérer Les Commandes')
            ->setPageTitle('edit', "Modifier la commande")
            ->setPageTitle ('detail','Détails de la commande')
            ->setEntityLabelInPlural ('Commandes')
            ->setDefaultSort(['id'=>'DESC']);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('createdAt','Créée le'),
            TextField::new('user.getUsername','Utilisateur'),
            TextareaField::new('delivery', 'Adresse de Livraison') ->onlyOnDetail()->renderAsHtml (),
            IntegerField::new('total','Total (en FCFA)'),
            TextField::new('carrierName','Transporteur')->hideOnIndex (),
            IntegerField::new('carrierPrice','Frais de port')->hideOnIndex (),
            ChoiceField::new('state','Etat')->setChoices([
                'Non payée'=>0,
                'Payée'=>1,
                'Préparation en cours'=>2,
                'Livraison en cours'=>3,
                'Commande Livrée'=>4
            ]),
            ArrayField::new('orderDetails','Produit(s)')->hideOnIndex()
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(DateTimeFilter::new('createdAt'))
            ->add(ChoiceFilter::new('state')->setChoices ([
                'Non payée'=>0,
                'Payée'=>1,
                'Préparation en cours'=>2,
                'Livraison en cours'=>3,
                'Commande Livrée'=>4
            ]))
            ->add('carrierName')
            ;
    }

}
