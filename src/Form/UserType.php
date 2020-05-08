<?php

namespace App\Form;

use App\Entity\Avatar;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add(
	        	'avatar',
		        AvatarType::class,
		        $this->getConfiguration(false, ['placeholder' => "Choisissez votre photo..."], [
			        'data_class' => Avatar::class])
	        )
            ->add(
            	'lastName',
	            textType::class,
                    $this->getConfiguration('Nom', ['placeholder' => "Votre nom.."])
                    )
            ->add(
            	'firstName',
	            textType::class,
	            $this->getConfiguration('Prénom', ['placeholder' => "Votre prénom.."])
	            )
            ->add(
            	'username',
	            textType::class,
	            $this->getConfiguration('Username', ['placeholder' => "Votre nom d'utilisateur.."])
	            )
            ->add(
            	'email',
	            EmailType::class,
	            $this->getConfiguration('Email', ['placeholder' => "Votre adresse email.."])
	            )
            ->add(
            	'hash',
	            PasswordType::class,
	            $this->getConfiguration('Mot de passe', ['placeholder' => "Choisissez un mot de passe !"])
	            )
	        ->add(
		        'passwordConfirm',
		        PasswordType::class,
		        $this->getConfiguration('Confirmation du mot de passe', ['placeholder' => "Veuillez confirmer votre mot de passe !"])
	        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}
