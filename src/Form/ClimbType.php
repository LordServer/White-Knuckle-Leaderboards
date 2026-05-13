<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Climb;
use App\Entity\Subcategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class ClimbType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        $builder
            ->add('rank')
            ->add('score')
            ->add('time')
            ->add('height')
            ->add('speed')
            ->add('notes')
            ->add('status')
            ->add('is_reviewed')
            ->add('media_url')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose a category',
                'attr' => [
                    'data-model' => 'on(change)|formValues.category',
                ],
            ]);
        ;

        $builder
            ->addDependent('subcategory', 'category', function (DependentField $field, ?Category $category) {
                if (!$category) {
                    $field->add(ChoiceType::class, [
                        'disabled' => true,
                        'placeholder' => 'Please select a Category',
                        'choices' => [],
                    ]);
                }

                $field->add(ChoiceType::class, [
                    'choices' => $category?->getSubcategory(),
                    'choice_label' => 'name',
                    'placeholder' => 'Please select a Category',
                ]);
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Climb::class,
        ]);
    }
}
