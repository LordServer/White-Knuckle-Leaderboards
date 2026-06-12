<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\UserStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminType extends AbstractType
{
    public function __construct(
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => UserStatus::ACTIVE,
                    'Suspended' => UserStatus::SUSPENDED,
                    'Banned' => UserStatus::BANNED,
                ],
                'choice_value' => fn (?UserStatus $status) => $status?->value,
                'choice_label' => fn (UserStatus $status) => ucfirst($status->value),
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

            $banDays = 30;

            if (
                null !== $user
                && null !== $user->getBannedUntil()
                && $user->getBannedUntil() > new \DateTime()
            ) {
                $now = new \DateTime();

                $interval = $now->diff($user->getBannedUntil());

                $banDays = $interval->days;
            }

            $form->add('banDays', IntegerType::class, [
                'mapped' => false,
                'required' => false,
                'data' => $banDays,
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
