<?php

namespace dlaser\AgendaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class RestriccionType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        ->add('cantidad', 'integer', array('required' => false, 'attr' => array('placeholder' => 'Cantidad de actividades')))
        ->add('cargo', 'entity', array(
                'class' => 'dlaser\\ParametrizarBundle\\Entity\\Cargo',
                'required' => true,
                'empty_value' => 'Seleccione una actividad',
                'query_builder' => function(EntityRepository $repositorio) {
                return $repositorio->createQueryBuilder('c')
                ->orderBy('c.nombre', 'ASC');
        }
        ))
        ->add('sede', 'entity', array(
                'class' => 'dlaser\\ParametrizarBundle\\Entity\\Sede',
                'required' => true,
                'empty_value' => 'Seleccione una sede',
                'query_builder' => function(EntityRepository $repositorio) {
                return $repositorio->createQueryBuilder('s')
                ->orderBy('s.nombre', 'ASC');
        }
        ))
        ->add('cliente', 'entity', array(
                'class' => 'dlaser\\ParametrizarBundle\\Entity\\Cliente',
                'required' => true,
                'empty_value' => 'seleccione un cliente',
                'query_builder' => function(EntityRepository $repositorio) {
                return $repositorio->createQueryBuilder('c')
                ->orderBy('c.nombre', 'ASC');
        }
        ))
        ;
    }

    public function getName()
    {
        return 'Restriccion';
    }
}