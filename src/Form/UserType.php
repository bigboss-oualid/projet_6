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
		        $this->getConfiguration(false, ['placeholder' => "Avatar..."], [
			        'data_class' => Avatar::class])
	        )
            ->add(
            	'lastName',
	            textType::class,
                    $this->getConfiguration('Nom', ['placeholder' => "Nom.."])
                    )
            ->add(
            	'firstName',
	            textType::class,
	            $this->getConfiguration('Prénom', ['placeholder' => "Prénom.."])
	            )
            ->add(
            	'username',
	            textType::class,
	            $this->getConfiguration('Username', ['placeholder' => "Nom d'utilisateur.."])
	            )
            ->add(
            	'email',
	            EmailType::class,
	            $this->getConfiguration('Email', ['placeholder' => "Adresse email.."])
	            )
            ->add(
            	'hash',
	            PasswordType::class,
	            $this->getConfiguration('Mot de passe', ['placeholder' => "Mot de passe !"])
	            )
	        ->add(
		        'passwordConfirm',
		        PasswordType::class,
		        $this->getConfiguration('Confirmation du mot de passe', ['placeholder' => "Confirmation mot de passe !"])
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
