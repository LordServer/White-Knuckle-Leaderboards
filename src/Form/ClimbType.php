<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Climb;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class ClimbType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entry = $options['data'];
        $locked = $entry && $entry->isReviewed() && !$this->security->isGranted('ROLE_DISCORD_ADMIN');

        $builder = new DynamicFormBuilder($builder);

        $builder
            ->add('score', null, [
                'disabled' => $locked,
            ])
            ->add('time', null, [
                'disabled' => $locked,
            ])
            ->add('height', null, [
                'disabled' => $locked,
            ])
            ->add('speed', null, [
                'disabled' => $locked,
            ])
            ->add('notes', null, [
                'disabled' => $locked,
            ])
            ->add('media_url', null, [
                'disabled' => $locked,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose a category',
                'disabled' => $locked,
                'attr' => [
                    'data-model' => 'on(change)|formValues.category',
                ],
            ])
            ->add('approve', SubmitType::class, [
                'label' => 'Approve',
            ])
            ->add('reject', SubmitType::class, [
                'label' => 'Reject',
            ])
        ;

        $builder
            ->addDependent('subcategory', 'category', function (DependentField $field, ?Category $category) use ($locked) {
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
                    'disabled' => $locked,
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
