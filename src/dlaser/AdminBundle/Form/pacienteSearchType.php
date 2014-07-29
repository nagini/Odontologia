<?php
namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class pacienteSearchType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder
		->add('nombre','text',array('label'=> 'Busqueda rapida:','property_path' => false,
				'attr' => array('placeholder' => 'Ingrese el nombre',
						'autofocus'=>'autofocus')))
				
		->add('option', 'choice', array(
				'label'=> 'Opcion de busqueda:',
				'property_path' => false,
				 'choices' => array(
				 		'cedula'=> 'Cedula',
				 		'nombre'=> 'Nombre',
				 		'apellido'=> 'Apellido',),
				'multiple'=>false,
				))
						;
	}

	public function getName()
	{
		return 'searchPaciente';
	}
}
