<?php

namespace App\Form;

use App\Classes\Search;
use App\Entity\ProductCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('string',TextType::class,[
                'label'=>false,
                'required'=>false,
                'attr'=>[
                    'placeholder'=>'Votre recherche ...',
                    'class'=>'textfield',
                    'name'=>'s'
                ]
            ])
            ->add('categories', EntityType::class, [
                'label'=>false,
                'required'=>false,
                'class'=>ProductCategory::class,
                'multiple'=>true,
                'expanded'=>true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=>Search::class,
            'method'=>'GET',
            'crsf_protection'=>false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}