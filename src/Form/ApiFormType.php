<?php

namespace App\Form;

use App\Entity\Api;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApiFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isDefault', CheckboxType::class, [
                'required' => false,
                'label' => 'Utiliser cette API par défaut'
            ])
            ->add('apiKey', TextType::class, [
                'label' => 'Clé API',
                'required' => true
            ])
            ->add('apiSecret', TextType::class, [
                'label' => 'Id Search Engine',
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Api::class,
        ]);
    }
}
