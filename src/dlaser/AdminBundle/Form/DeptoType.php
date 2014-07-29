<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class DeptoType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        ->add('nombre', 'entity', array(
                'class' => 'dlaser\\ParametrizarBundle\\Entity\\Depto',
                'query_builder' => function(EntityRepository $repositorio) {
                    return $repositorio->createQueryBuilder('d')
                        ->orderBy('d.nombre', 'ASC');
                },
                'preferred_choices' => array(24)                
            ))
        ;
    }
    
    public function getDefaultOptions(array $opciones)
    {
        return array('data_class' => 'dlaser\ParametrizarBundle\Entity\Depto');
    }
    

    public function getName()
    {
        return 'Departamento';
    }
}