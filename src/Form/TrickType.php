<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
            	'title',
	            TextType::class,
	            $this->getConfiguration("Titre", ['placeholder' => "Tapez le titre de la figure"],['required' => false])
            )
	        ->add(
	        	'category',
		        EntityType::class,
		        $this->getConfiguration("Catégorie", [],
			        [
			        	'class' => Category::class,
				        'choice_label' => function(?Category $category) {
					        return $category ? strtoupper($category->getName()) : '';
				        },
				        'placeholder' => 'Choisir un groupe',
			        ])
		        )
            ->add(
            	'description',
	            TextareaType::class,
	            $this->getConfiguration("Description",
		            ['placeholder' => "Tapez la description de la figure"],
	                ['required' => false]
	            )
            )
            ->add(
            	'published',
	            CheckboxType::class,
	            $this->getConfiguration("Publier la figure", [], ['required' => false])

             )

	        ->add('illustrations', CollectionType::class, [
		        'entry_type'   	=> IllustrationType::class,
		        'prototype'		=> true,
		        'allow_add'		=> true,
		        'allow_delete'	=> true,
		        'by_reference' 	=> false,
		        'required'		=> false,
		        'label'			=> false,
	        ])

	        ->add('videos', CollectionType::class, [
		        'entry_type'   	=> VideoType::class,
		        'prototype'		=> true,
		        'allow_add'		=> true,
		        'allow_delete'	=> true,
		        'by_reference' 	=> false,
		        'required'		=> false,
		        'label'			=> false,
	        ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
	        'error_bubbling' => true
        ]);
    }

}
