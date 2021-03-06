<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                "constraints" => [
                    new NotNull(),
                    new NotBlank()
                ]
            ])
            ->add('firstname', TextType::class, [
                "constraints" => [
                    new NotNull(),
                ]
            ])
            ->add('lastname', TextType::class, [
                "constraints" => [
                    new NotNull(),
                ]
            ])
            ->add('password', PasswordType::class,[
                "constraints" => [
                    new NotNull(),
                ]
            ])
            ->add('passwordConfirm', PasswordType::class, [
                'mapped' => false,
                "constraints" =>[
                    new NotNull(),
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
