<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Livre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;



class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ref', IntegerType::class, [
                'label' => 'Reference',
                'attr' => ['class' => 'form-control']
            ])
            ->add('title', TextType::class, [
                'label' => 'Title',
                'attr' => ['class' => 'form-control']
            ])
            ->add('nbrPages', IntegerType::class, [
                'label' => 'Number of Pages',
                'attr' => ['class' => 'form-control']
            ])
            ->add('picture', TextType::class, [
                'label' => 'Picture (optional)',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('author', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'username',
                'label' => 'Author',
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
