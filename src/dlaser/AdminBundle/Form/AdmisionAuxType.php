<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class AdmisionAuxType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        ->add('cliente', 'entity', array('class' => 'dlaser\ParametrizarBundle\Entity\Cliente', 'empty_value' => 'Elige una aseguradora', 'required' => true))
        ->add('cargo', 'entity', array('required' => true, 'class' => 'dlaser\ParametrizarBundle\Entity\Cargo', 'empty_value' => 'Elige una sede'))
        ->add('sede', 'entity', array('required' => true, 'class' => 'dlaser\ParametrizarBundle\Entity\Sede', 'empty_value' => 'Elige una sede'))
        ->add('autorizacion', 'text', array('required' => true, 'attr' => array('placeholder' => 'Número de autorización', 'autofocus' => 'autofocus')))
        ->add('copago', 'integer', array('required' => true))
        ->add('descuento', 'integer', array('required' => false))
        ->add('observacion', 'text', array('property_path' => null, 'required' => false, 'attr' => array('placeholder' => 'Ingrese una observación')))
        ;
    }

    public function getName()
    {
        return 'auxAdmision';
    }
}