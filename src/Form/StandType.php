<?php

namespace App\Form;

use App\Entity\Forum;
use App\Entity\Stand;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                    'label' => 'Titre',
                ])
            ->add('picture', null, [
                'label' => 'Image',
            ])
            ->add('description', null, [
                'label' => 'Description',
            ])
            ->add('capacity',null, [
                    'label' => 'Capacité',
                ])
            ->add('duration', null, [
                'label' => 'Durée',
                'widget' => 'single_text',
            ])
            ->add('forum', EntityType::class, [
                'class' => Forum::class,
                'choice_label' => function (Forum $forum) {
                    return $forum->getTitle();
                },
                'placeholder' => 'Choose a forum',
                'attr' => ['class' => 'mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stand::class,
        ]);
    }
}
