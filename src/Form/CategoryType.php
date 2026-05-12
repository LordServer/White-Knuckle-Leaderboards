<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\RankMethod;
use App\Entity\Subcategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('rules', TextareaType::class, [
                'required' => true,
            ])
            ->add('is_archived', CheckboxType::class, [
                'required' => false,
            ])
            ->add('subcategory', EntityType::class, [
                'class' => Subcategory::class,
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('rank_method', EntityType::class, [
                'class' => RankMethod::class,
                'choice_label' => 'name',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'subcategory_item',
        ]);
    }
}
