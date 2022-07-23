<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
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

    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation','Préparation en cours','fas fa-box-open')
            ->linkToCrudAction('updatePreparation');
        $updateDelivering = Action::new('updateDelivering','Livraison en cours','fas fa-truck')
            ->linkToCrudAction('updateDelivering');

       return $actions
           ->add('index','detail')
           ->add('detail', $updatePreparation)
           ->add('detail', $updateDelivering)
           ->disable(Action::NEW);
    }

    public function updatePreparation(AdminContext $context){
        $order = $context->getEntity()->getInstance();

        $order->setState(2);
        $this->entityManager->flush();

        $this->addFlash('notice',"<span style='color: green'  > <strong> La commande ".$order->getReference()." a bien été mise à jour  </strong> </span>");
        $url = $this->adminUrlGenerator
            ->setDashboard(DashboardController::class)
            ->setController(OrderCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function updateDelivering(AdminContext $context){
        $order = $context->getEntity()->getInstance();

        $order->setState(3);
        $this->entityManager->flush();

        $this->addFlash('notice',"<span style='color: orange'  > <strong> La commande ".$order->getReference()." a bien été mise à jour  </strong> </span>");
        $url = $this->adminUrlGenerator
            ->setDashboard(DashboardController::class)
            ->setController(OrderCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id'=>'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt','Créée le'),
            TextField::new('user.getUsername','Utilisateur'),
            TextEditorField::new('delivery', 'Adresse de Livraison')->onlyOnDetail(),
            MoneyField::new('total')->setCurrency('XAF'),
            TextField::new('carrierName','Transporteur'),
            MoneyField::new('carrierPrice','Frais de port')->setCurrency('XAF'),
            ChoiceField::new('state')->setChoices([
                'Non payée'=>0,
                'Payée'=>1,
                'Préparation en cours'=>2,
                'Livraison en cours'=>3
            ]),
            ArrayField::new('orderDetails','Produits acheté')->hideOnIndex()
        ];
    }

}
