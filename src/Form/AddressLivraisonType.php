<?php

namespace App\Form;

use App\Entity\AddressLivraison;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
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
                'label'=>false,
                'attr'=>[
                    'placeholder'=>'Nommez l\'adresse'
                ]
            ])
            ->add('firstname', TextType::class,[
                'label'=>false,
                'attr'=>[
                    'placeholder'=>'Entrez prénom'
                ]
            ])
            ->add('lastname', TextType::class,[
                'label'=>false,
                'attr'=>[
                    'placeholder'=>'Entré votre nom'
                ]
            ])
            ->add('company', TextType::class,[
                'label'=>false,
                'attr'=>[
                    'placeholder'=>'(facultatif) Entrez le nom de votre société'
                ]
            ])
            ->add('address', TextType::class,[
                'label'=>false,
                'attr'=>[
                    'placeholder'=>'Entrez votre adress (Exemple: 8 rue des lylas..).'
                ]
            ])
            ->add('postal', TextType::class,[
                'label'=>false,
                'attr'=>[
                    'placeholder'=>'Entrez votre code postal'
                ]
            ])
            ->add('city', TextType::class,[
                'label'=>false,
                'attr'=>[
                    'placeholder'=>'Entrez votre ville'
                ]
            ])
            ->add('country', CountryType::class,[
                'label'=>false,
                'attr'=>[
                    'placeholder'=>'Votre pays'
                ]
            ])
            ->add('phone', TelType::class,[
                'label'=>false,
                'attr'=>[
                    'placeholder'=>'Numéro de téléphone'
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label'=>'Valider',
                'attr'=>[
                    'class'=>'custom-btn custom-btn--medium custom-btn--style-1'
                ]
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
