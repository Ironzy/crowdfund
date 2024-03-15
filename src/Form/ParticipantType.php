<?php

namespace App\Form;

use App\Entity\Campaign;
use App\Entity\Participant;
use App\Entity\Payment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null)
            ->add('email', EmailType::class)
            /*->add('campaign', EntityType::class, [
                'class' => Campaign::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])*/
            /*->add('payment', EntityType::class, [
                'class' => Payment::class,
                'choice_label' => 'id',
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
