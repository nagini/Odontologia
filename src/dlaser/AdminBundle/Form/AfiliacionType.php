<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class AfiliacionType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        ->add('cliente', 'entity', array('class' => 'dlaser\ParametrizarBundle\Entity\Cliente', 'empty_value' => 'Elige una aseguradora', 'required' => true))
        ->add('observacion', 'text', array('required' => false, 'attr' => array('placeholder' => 'Ingrese alguna observación')))
                
        ;
    }

    public function getName()
    {
        return 'newAfiliacion';
    }
}