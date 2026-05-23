<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimecodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('hours', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'HH',
                ],
                'empty_data' => 0,
            ])
            ->add('minutes', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'MM',
                ],
                'empty_data' => 0,
            ])
            ->add('seconds', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'SS',
                ],
                'empty_data' => 0,
            ])
            ->add('milliseconds', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'MS',
                ],
                'empty_data' => 0,
            ])
        ;

        /*
         * Convert seconds -> fields
         */
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $seconds = $event->getData();

            if (null === $seconds) {
                $seconds = 0;
            }

            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $wholeSeconds = $seconds % 60;
            $milliseconds = round(($seconds - floor($seconds)) * 1000);

            $event->setData([
                'hours' => $hours,
                'minutes' => $minutes,
                'seconds' => $wholeSeconds,
                'milliseconds' => $milliseconds,
            ]);
        });

        /*
         * Convert fields -> seconds
         */
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            $totalSeconds =
                ($data['hours'] * 3600)
                + ($data['minutes'] * 60)
                + $data['seconds']
                + $data['milliseconds'] / 1000;

            $event->setData($totalSeconds);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => true,
        ]);
    }
}
