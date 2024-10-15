<?php

namespace App\Form;

use App\Entity\Forum;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
            'label' => 'Titre :'
            ])
            ->add('picture', null,[
                'label' => 'Image :',
            ])
            ->add('description', null, [
                'label' => 'Description :'
            ])
            ->add('location', null, [
                'label' => 'Lieu :'
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'Organisateur :',
                'choice_label' => function (User $user)
                {
                    return $user->getFirstName() . ' ' . $user->getLastName();
                },
                'placeholder' => 'Sélectionnez un utilisateur',
                'multiple' => false,
                'expanded' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Forum::class,
        ]);
    }
}
