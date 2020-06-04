<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
            	'oldPassword',
	            PasswordType::class,
	            $this->getConfiguration('Ancien mot de passe', ['placeholder' => "Mot de passse actuel..."])
            )
	        ->add(
		        'newPassword',
		        PasswordType::class,
		        $this->getConfiguration('Nouveau mot de passe', ['placeholder' => "Nouveau mot de passse..."])
	        )
	        ->add(
		        'confirmPassword',
		        PasswordType::class,
		        $this->getConfiguration('Confirmez nouveau mot de passe', ['placeholder' => "Confirmez le nouveau mot de passe..."])
	        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
