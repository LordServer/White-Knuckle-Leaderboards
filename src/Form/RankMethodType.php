<?php

namespace App\Form;

use App\Entity\RankMethod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RankMethodType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        if ($options['delete']) {
            $builder
                ->add('delete', SubmitType::class, [
                    'label' => 'Delete Rank Method',
                ]);

            return;
        }

        $builder
            ->add('name')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RankMethod::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'rank_method_item',
            'delete' => false,
        ]);

        $resolver->setAllowedTypes('delete', 'bool');
    }
}
