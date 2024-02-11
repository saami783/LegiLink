<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'required' => true,
                'attr' => ['class' => 'form-floating']
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => ['class' => 'form-floating']
            ])
            ->add('surname', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
            ])
            ->add('profession', ChoiceType::class, [
                'choices' => [
                    'Élève' => 'student',
                    'Professeur' => 'teacher',
                    'Autre' => 'other',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
                'mapped' => true
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => "J'accepte les Conditions d'utilisation et la Politique de Confidentialité du site.",
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => "S'inscrire",
                'attr' => ['class' => 'btn-secondary']
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
