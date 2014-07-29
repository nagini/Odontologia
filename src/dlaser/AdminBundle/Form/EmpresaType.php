<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class EmpresaType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        ->add('nit', 'text', array('required' => true, 'attr' => array('placeholder' => 'Ingrese el nit', 'autofocus'=>'autofocus'))) 
        ->add('nombre', 'text', array('required' => true, 'attr' => array('placeholder' => 'Ingrese nombre')))
        ;
    }

    public function getName()
    {
        return 'newEmpresa';
    }
}