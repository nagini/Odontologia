<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ContratoType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        ->add('contacto', 'text', array('required' => true, 'attr' => array('placeholder' => 'Ingrese el nombre del contacto', 'autofocus'=>'autofocus')))
        ->add('cargo', 'text', array('required' => false, 'attr' => array('placeholder' => 'Ingrese el cargo del contacto')))
        ->add('telefono', 'integer', array('required' => false, 'label' => 'Teléfono', 'attr' => array('placeholder' => 'Número telefonico')))
        ->add('celular', 'text', array('required' => false, 'attr' => array('placeholder' => 'Número movil')))
        ->add('email', 'text', array('attr' => array('placeholder' => 'Correo electrónico')))
        ->add('estado', 'choice', array('required' => true, 'choices' => array('A' => 'Activo', 'I' => 'Inactivo')))
        ->add('porcentaje', 'percent', array('required' => true, 'precision' => 0, 'attr' => array('placeholder' => 'Porcentaje pactado')))
        ->add('observacion', 'text', array('required' => false, 'attr' => array('placeholder' => 'Ingrese observación del contacto')))
        ->add('sede', 'entity', array('required' => true, 'class' => 'dlaser\ParametrizarBundle\Entity\Sede', 'empty_value' => 'Elige una sede'))                
        ;
    }

    public function getName()
    {
        return 'newContrato';
    }
}