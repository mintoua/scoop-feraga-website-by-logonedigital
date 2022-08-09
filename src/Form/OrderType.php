<?php

namespace App\Form;

use App\Entity\AddressLivraison;
use App\Entity\Carrier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $builder
            ->add('addresses', EntityType::class,[
                'label'=>false,
                'required'=>true,
                'class'=>AddressLivraison::class,
                'choices'=>$user->getAddressLivraisons(),
                'multiple'=>false,
                'expanded'=>true,
                'attr'=>[
                    'class'=>'d-flex flex-column justify-content-start'
                ]
            ])
            ->add('carriers', EntityType::class,[
                'label'=>'Choississez un transporteur',
                'required'=>true,
                'class'=>Carrier::class,
                'multiple'=>false,
                'expanded'=>true,
                'attr'=>[
                    'class'=>'d-flex flex-column justify-content-start'
                ]
            ])
            ->add('submit',SubmitType::class,[
                'label'=>'Valider',
                'attr'=>[
                    'class'=>'custom-btn custom-btn--medium custom-btn--style-1 btn-cart',
                    'role'=>'button'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'user'=>array()
        ]);
    }
}
