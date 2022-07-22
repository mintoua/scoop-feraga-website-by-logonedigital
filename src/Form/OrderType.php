<?php

namespace App\Form;

use App\Entity\AddressLivraison;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
