<?php
/**
 * Created by IntelliJ IDEA.
 * User: BigBoss
 * Date: 27/04/2020
 * Time: 02:59
 */

namespace App\Form;


use App\Entity\Avatar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvatarType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('imageFile', FileType::class,[
				'label'    => false,
				'required' => false
			]);

	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Avatar::class
		]);
	}

}