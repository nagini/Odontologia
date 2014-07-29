<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FacturaType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        ->add('autorizacion', 'text', array('required' => true, 'attr' => array('placeholder' => 'Número de autorización', 'autofocus' => 'autofocus')))
        ->add('copago', 'integer', array('required' => true))
        ->add('descuento', 'integer', array('required' => false))
        ->add('observacion', 'text', array('property_path' => null, 'required' => false, 'attr' => array('placeholder' => 'Ingrese una observación')))
        ;
    }

    public function getName()
    {
        return 'newAdmision';
    }
}