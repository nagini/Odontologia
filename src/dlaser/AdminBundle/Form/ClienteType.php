<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ClienteType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        ->add('nit', 'text', array('required' => true, 'attr' => array('placeholder' => 'Ingrese el nit', 'autofocus'=>'autofocus',)))
        ->add('cod_eps', 'text', array('required' => true, 'label' => 'Código de la eps', 'attr' => array('placeholder' => 'Ingrese el código de la eps')))
        ->add('nombre', 'text', array('required' => true, 'attr' => array('placeholder' => 'Ingrese nombre')))
        ->add('razon', 'text', array('required' => false, 'label' => 'Razón social', 'attr' => array('placeholder' => 'razón social')))
        ->add('direccion', 'text', array('required' => false, 'attr' => array('placeholder' => 'Dirección')))
        ->add('telefono', 'text', array('required' => false, 'attr' => array('placeholder' => 'Teléfono')))
        ;
    }

    public function getName()
    {
        return 'newCliente';
    }
}