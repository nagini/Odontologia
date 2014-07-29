<?php

namespace dlaser\AgendaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class AgendaMedicaType extends AbstractType
{    
    private $options;
    
    public function __construct(array $options = null)
    {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        
        $id = $this->options['user'];
        
        $builder        
        ->add('sede', 'entity', array(
                'class' => 'dlaser\\ParametrizarBundle\\Entity\\Sede',
                'property_path' => false,
                'empty_value' => 'Seleccione una sede',
                'query_builder' => function(EntityRepository $er) use ($id) {
                        return $er->createQueryBuilder('s','u')
                        ->leftJoin("s.usuario", "u")
                        ->where("u.id = :id")
                        ->setParameter('id', $id);
                        }
        ))
        ;
    }

    public function getName()
    {
        return 'AgendaMedica';
    }
    
    public function getDefaultOptions(array $options){    
        return $options;
    }
}