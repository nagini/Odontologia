<?php

namespace dlaser\AgendaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class AgendaType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        ->add('fecha_inicio', 'datetime', array('label' => 'Fecha de inicio', 'years' => range(2014, 2015), 'required' => true))
        ->add('fecha_fin', 'datetime', array('label' => 'Fecha de fin', 'years' => range(2014, 2015), 'required' => true))
        ->add('intervalo', 'integer', array('attr' => array('placeholder' => 'Ingrese el tiempo de atención'), 'required' => true))
        ->add('estado', 'choice', array('choices' => array('A' => 'Activa', 'I' => 'Inactiva'), 'required' => true))
        ->add('nota', 'text', array('attr' => array('placeholder' => 'Ingrese su nota'), 'required' => false))
        ->add('sede', 'entity', array(
                'class' => 'dlaser\\ParametrizarBundle\\Entity\\Sede',
                'required' => true,
                'empty_value' => 'Selecciona una sede',
                'query_builder' => function(EntityRepository $repositorio) {
                return $repositorio->createQueryBuilder('s')
                ->orderBy('s.nombre', 'ASC');
        }
        ))
        ->add('usuario', 'entity', array(
                'class' => 'dlaser\\UsuarioBundle\\Entity\\Usuario',
                'required' => true,
                'empty_value' => 'Selecciona un usuario',
                'query_builder' => function(EntityRepository $repositorio) {
                return $repositorio->createQueryBuilder('u')
                ->where('u.perfil = :role')
                ->setParameter('role', 'ROLE_MEDICO')
                ->orderBy('u.nombre', 'ASC');
        }
        ))
        ;
    }

    public function getName()
    {
        return 'Agenda';
    }
}