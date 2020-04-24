<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre", "Tapez le titre de la figure"))
	        ->add('category', EntityType::class, [
		        'label' => 'Catégorie',
		        'class' => Category::class,
		        'choice_label' => 'name'
	        ])
            ->add('description', TextareaType::class, $this->getConfiguration("Description", "Tapez la description de la figure"))
            ->add('published')

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }

	/**
	 * @param string $label
	 * @param null|string $placeholder
	 *
	 * @return array
	 */
    private function getConfiguration($label, $placeholder = null): array {
    	return [
    		'label' => $label,
		    'attr'  => [
		    	'placeholder' => $placeholder
		    ]
	    ];
    }
}
