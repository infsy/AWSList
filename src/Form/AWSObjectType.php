<?php

namespace App\Form;

use App\Entity\AWSObject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AWSObjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('AWSId')
            ->add('AWSType')
            ->add('AWSName')
            ->add('AWSRegion')
            ->add('AWSSubscription')
            ->add('AWSDeletionTime')
            ->add('AWSFirstDetection')
            ->add('AWSLastDetection')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AWSObject::class,
        ]);
    }
}
