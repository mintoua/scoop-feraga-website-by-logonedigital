<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                
                "constraints" => [
                    new NotNull(),
                    new NotBlank(),
                    new Email([
                        'message'=>'cette email n\'est pas vilide'
                    ])
                ]
            ])
            ->add('firstname', TextType::class, [
                "constraints" => [
                    new NotNull(),
                    new Length([
                        'min'=>3,
                        "minMessage"=>"minimum {{ limit }} caractères."
                    ]),
                    new Regex(
                    [
                        "pattern"=>"/\d/",
                        "match"=>false,
                        "message"=>"le nom ne doit pas contenir de chiffre."
                    ]
                    )
                ]
            ])
            ->add('lastname', TextType::class, [
                "constraints" => [
                    new Length([
                        'min'=>3,
                        "minMessage"=>"minimum {{ limit }} caractères."
                    ]),
                    new Regex(
                    [
                        "pattern"=>"/\d/",
                        "match"=>false,
                        "message"=>"le prenom ne doit pas contenir de chiffre."
                    ]
                    )
                ]
            ])
            ->add("password", RepeatedType::class, [
                
                'type'=>PasswordType::class,
                'invalid_message'=> 'le mot de passe et la confirmation doivent être identique.',
                'first_options'=>[
                    'constraints'=>[
                        new Regex(
                    [
                        "pattern"=>"/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,20}$/",
                        "match"=>true,
                        "message"=>"mot de passe invalid"
                    ]
                    ),
                    ],
                    'help'=> 'Le mot de passe doit contenir au moins 8 caractères, dont au moins: une Majuscule, un chiffre, un caractère spéciale.',
                    ],
                'second_options'=>[
                    
                ]
            ])
            
            ->add('rgpd', CheckboxType::class, [
                "constraints" => [
                    new IsTrue(['message'=>"vous n'avez pas acceptez les conditions générales d'utilisatioin"])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
