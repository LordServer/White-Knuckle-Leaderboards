<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\UserStatus;
use App\Service\RolePermissionService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminType extends AbstractType
{
    public function __construct(
        private readonly RolePermissionService $rolePermissionService,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'choices' => $this->rolePermissionService->getAssignableRoles(),
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => UserStatus::ACTIVE,
                    'Suspended' => UserStatus::SUSPENDED,
                    'Banned' => UserStatus::BANNED,
                ],
                'choice_value' => fn (?UserStatus $status) => $status?->value,
                'choice_label' => fn (UserStatus $status) => ucfirst($status->value),
            ])
            ->add('banDays', IntegerType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Temporarily ban (Days)',
                'attr' => [
                    'min' => 1,
                    'max' => 3650,
                ],
                'help' => 'Leave blank for a permanent ban.'
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
