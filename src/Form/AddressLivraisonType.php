<?php

namespace App\Form;

use App\Entity\AddressLivraison;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressLivraisonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label'=>'Quel nom souhaitez-vous donner à votre adresse?',
                'attr'=>[
                    'placeholder'=>'Nommez votre adresse'
                ]
            ])
            ->add('firstname', TextType::class,[
                'label'=>'Quel nom souhaitez-vous donner à votre adresse?',
                'attr'=>[
                    'placeholder'=>'Nommez votre adresse'
                ]
            ])
            ->add('lastname', TextType::class,[
                'label'=>'Quel nom souhaitez-vous donner à votre adresse?',
                'attr'=>[
                    'placeholder'=>'Nommez votre adresse'
                ]
            ])
            ->add('company', TextType::class,[
                'label'=>'Quel nom souhaitez-vous donner à votre adresse?',
                'attr'=>[
                    'placeholder'=>'Nommez votre adresse'
                ]
            ])
            ->add('address', TextType::class,[
                'label'=>'Quel nom souhaitez-vous donner à votre adresse?',
                'attr'=>[
                    'placeholder'=>'Nommez votre adresse'
                ]
            ])
            ->add('postal', TextType::class,[
                'label'=>'Quel nom souhaitez-vous donner à votre adresse?',
                'attr'=>[
                    'placeholder'=>'Nommez votre adresse'
                ]
            ])
            ->add('city', TextType::class,[
                'label'=>'Quel nom souhaitez-vous donner à votre adresse?',
                'attr'=>[
                    'placeholder'=>'Nommez votre adresse'
                ]
            ])
            ->add('country', TextType::class,[
                'label'=>'Quel nom souhaitez-vous donner à votre adresse?',
                'attr'=>[
                    'placeholder'=>'Nommez votre adresse'
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label'=>'Ajouter mon adresse'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddressLivraison::class,
        ]);
    }
}
