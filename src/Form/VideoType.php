<?php

namespace App\Form;


use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('embed_code', TextareaType::class, [
				'label' => false,
				'attr' => [
					'placeholder' 	=> "Entrer le code inegré de la video",
					//'style' => 'display:none'
				]
			])
		;

	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Video::class,
		]);
	}

}