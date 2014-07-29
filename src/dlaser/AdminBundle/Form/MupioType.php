<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class MupioType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        ->add('municipio', 'entity', array(
                'class' => 'dlaser\\ParametrizarBundle\\Entity\\Mupio',
                'query_builder' => function(EntityRepository $repositorio) {
                    return $repositorio->createQueryBuilder('m')
                        ->orderBy('m.nombre', 'ASC');
                }                
            ))
        ;
    }
    
    public function getDefaultOptions(array $opciones)
    {
        return array('data_class' => 'dlaser\ParametrizarBundle\Entity\Mupio');
    }
    

    public function getName()
    {
        return 'Municipio';
    }
}