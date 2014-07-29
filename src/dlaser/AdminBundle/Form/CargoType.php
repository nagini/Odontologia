<?php
namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CargoType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        ->add('cups', 'text', array('label' => 'Código', 'required' => true, 'attr' => array('placeholder' => 'Código cups de la actividad', 'autofocus'=>'autofocus')))
        ->add('nombre', 'text', array('required' => true, 'attr' => array('placeholder' => 'Nombre de la actividad')))
        ->add('indicacion', 'textarea', array('required' => false, 'label' => 'Indicación', 'attr' => array('placeholder' => 'Indicación de la actividad')))
        ->add('valor', 'integer', array('required' => false, 'attr' => array('placeholder' => 'Valor de la actividad')))
        ;
    }

    public function getName()
    {
        return 'newCargo';
    }
}