<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ActividadType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        ->add('cargo', 'entity', array('required' => true, 'class' => 'dlaser\ParametrizarBundle\Entity\Cargo', 'empty_value' => 'Elige un cargo'))
        ->add('estado', 'choice', array('required' => true, 'choices' => array('A' => 'Activo', 'I' => 'Inactivo')))
        ->add('precio', 'integer', array('required' => false, 'attr' => array('placeholder' => 'Valor de la actividad', 'autofocus'=>'autofocus')))
        
                
        ;
    }

    public function getName()
    {
        return 'newActividad';
    }
}