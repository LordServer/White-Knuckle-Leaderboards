<?php

namespace App\Form;

use App\Entity\ApiToken;
use App\Service\ScopePermissionService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApiTokenType extends AbstractType
{
    public function __construct(
        private readonly ScopePermissionService $scopePermissionService,
        private readonly Security $security,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['delete']) {
            $builder
                ->add('delete', SubmitType::class, [
                    'label' => 'Delete API token',
                ]);

            return;
        }

        $builder
            ->add('isEnabled', CheckboxType::class, [
                'required' => false,
            ])
            ->add('description')
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $token = $event->getData();
            if (!$token || null === $token->getId()) {
                $event->getForm()
                    ->add('token')
                    ->add('scopes', ChoiceType::class, [
                        'choices' => $this->scopePermissionService->getAssignableScopes(),
                        'multiple' => true,
                    ])
                    ->add('expiresAt', DateTimeType::class, [
                        'data' => new \DateTimeImmutable('+1 year'),
                        'required' => false,
                        'attr' => [
                            'data-token-expiry-target' => 'expiresAt',
                        ],
                    ]);
                if ($this->security->isGranted('ROLE_ADMIN')) {
                    $event->getForm()
                        ->add('neverExpires', CheckboxType::class, [
                            'mapped' => false,
                            'required' => false,
                            'attr' => [
                                'data-token-expiry-target' => 'checkbox',
                                'data-action' => 'change->token-expiry#toggle',
                            ],
                        ]);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApiToken::class,
            'delete' => false,
        ]);

        $resolver->setAllowedTypes('delete', 'bool');
    }
}
