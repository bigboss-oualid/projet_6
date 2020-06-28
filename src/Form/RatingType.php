<?php

namespace App\Form;

use App\Entity\Rating;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RatingType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('rating', IntegerType::class, $this->getConfiguration(false, ['min' => 0, 'max' => 5, 'placeholder' => "?/5"]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Rating::class,
        ]);
    }
}
