<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class PacienteType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        ->add('tipo_id', 'choice', array('required' => true, 'choices' => array('CC' => 'Cedula', 'RC' => 'Registro civil', 'CE' => 'Cedula de extranjeria', 'TI' => 'Tarjeta de identidad'), 'label' => 'Tipo identificación'))
        ->add('identificacion', 'text', array('required' => true, 'label' => 'Identificación', 'attr' => array('placeholder' => 'Número de identificación', 'autofocus' => 'autofocus')))
        ->add('pri_nombre', 'text', array('required' => true, 'label' => 'Primer nombre', 'attr' => array('placeholder' => 'Ingrese primer nombre')))
        ->add('seg_nombre', 'text', array('required' => false, 'label' => 'Segundo nombre', 'attr' => array('placeholder' => 'Ingrese segundo nombre')))
        ->add('pri_apellido', 'text', array('required' => true, 'label' => 'Primer apellido', 'attr' => array('placeholder' => 'Ingrese primer apellido')))
        ->add('seg_apellido', 'text', array('required' => false, 'label' => 'Segundo apellido', 'attr' => array('placeholder' => 'Ingrese segundo apellido')))
        ->add('f_n', 'date', array('required' => true, 'label' => 'Fecha de nacimiento', 'format' => 'dd-MMM-yyyy', 'empty_value' => array('year' => 'Año', 'month' => 'Mes', 'day' => 'Día'), 'years' => range(1900, 2012)))
        ->add('sexo', 'choice', array('required' => true, 'choices' => array('F' => 'Femenino','M' => 'Masculino')))
        ->add('depto', 'choice', array('required' => true, 'choices' => array('24' => 'VALLE')))
        ->add('mupio', 'choice', array('required' => true, 'choices' => array('' => '--')))
        ->add('direccion', 'text', array('required' => true, 'label' => 'Dirección', 'attr' => array('placeholder' => 'Domicilio')))
        ->add('zona', 'choice', array('required' => true, 'choices' => array('U' => 'Urbana', 'R' => 'Rural')))
        ->add('telefono', 'integer', array('required' => false, 'label' => 'Teléfono', 'attr' => array('placeholder' => 'Número teléfonico')))
        ->add('movil', 'text', array('required' => false, 'label' => 'Movil', 'attr' => array('placeholder' => 'Número Movil')))
        ->add('email', 'email', array('required' => false, 'label' => 'Email', 'attr' => array('placeholder' => 'Ingrese email principal')))        
        ;
    }

    public function getName()
    {
        return 'newPaciente';
    }
}