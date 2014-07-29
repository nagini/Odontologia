<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SedeType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        ->add('nombre', 'text', array('required' => true, 'attr' => array('placeholder' => 'Ingrese el nombre', 'autofocus'=>'autofocus'))) 
        ->add('ciudad', 'text', array('required' => true, 'attr' => array('placeholder' => 'Ingrese la ciudad')))
        ->add('direccion', 'text', array('required' => true, 'attr' => array('placeholder' => 'Ingrese la dirección')))
        ->add('telefono', 'integer', array('required' => true, 'attr' => array('placeholder' => 'Ingrese el teléfono')))
        ->add('movil', 'text', array('required' => false, 'attr' => array('placeholder' => 'Ingrese el movil')))
        ->add('email', 'email', array('required' => true, 'attr' => array('placeholder' => 'Ingrese el email')))
        ;
    }

    public function getName()
    {
        return 'newSede';
    }
}