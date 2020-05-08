<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add(
		        'avatar',
		        AvatarType::class
	        )
	        ->add(
		        'lastName',
		        textType::class,
		            [
		            	'label' => "Nom"
		            ]
	        )
	        ->add(
		        'firstName',
		        textType::class,
		        [
			        'label' => "Prénom"
		        ]
	        )
	        ->add(
		        'email',
		        EmailType::class
	        )
	        /*->add(
		        'hash',
		        PasswordType::class,
		        [
			        'label' => "Mot de passe",
			        'required' => false
		        ]
	        )
	        ->add(
		        'passwordConfirm',
		        PasswordType::class,
		        [
			        'label' => "Confirmation du mot de passe",
			        'required' => false
		        ]
	        )*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
