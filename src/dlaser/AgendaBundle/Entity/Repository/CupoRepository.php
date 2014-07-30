<?php

namespace dlaser\AgendaBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class CupoRepository extends EntityRepository
{
	
	public function findInformeGeneral($con_sede,$desde,$hasta)
	{
		$em = $this->getEntityManager();
		$dql= " SELECT
					COUNT(c.estado) AS cantidad,
					car.nombre,
			    	car.cups,					
					a.fechaInicio,
					c.estado,
					a.nota as agenda,
					s.nombre as sede
    			FROM
    				AgendaBundle:Cupo c
    			JOIN
    				c.cargo car
    			JOIN
    				c.paciente p
    			JOIN
    				c.agenda a
				JOIN
    				a.sede s
    			WHERE
			    	c.hora > :inicio AND
			    	c.hora <= :fin
			    	".$con_sede."
			    GROUP BY c.estado, c.cargo, c.agenda, a.sede  ORDER BY c.estado ASC";
	
		$query = $em->createQuery($dql);
	
		$query->setParameter('inicio', $desde);
		$query->setParameter('fin', $hasta);
			
		return $query->getResult();
	}
	
	public function findInformeCargo($con_sede,$desde,$hasta)
	{
		$em = $this->getEntityManager();
		$dql= " SELECT			    	
					COUNT(c.estado) AS cantidad,
					car.nombre,			    	
			    	car.cups,		
					c.nota as cupo,
					c.hora,
					c.estado,
					a.nota as agenda,
					s.nombre as sede
    			FROM
    				AgendaBundle:Cupo c
    			JOIN
    				c.cargo car
    			JOIN
    				c.paciente p    			
    			JOIN
    				c.agenda a
				JOIN
    				a.sede s				
    			WHERE
			    	c.hora > :inicio AND
			    	c.hora <= :fin
			    	".$con_sede."
			    GROUP BY c.estado, c.cargo ORDER BY c.estado ASC";
	
		$query = $em->createQuery($dql);
	
		$query->setParameter('inicio', $desde);
		$query->setParameter('fin', $hasta);		
			
		return $query->getResult();
	}

	public function findInformePaciente($con_sede,$desde,$hasta)
	{
		$em = $this->getEntityManager();
		$dql= " SELECT			    	
					COUNT(c.estado) AS cantidad,					
					c.estado,
					p.priNombre, 
                    p.segNombre, 
                    p.priApellido, 
                    p.segApellido,
					p.identificacion,
					p.movil,					
					s.nombre as sede
    			FROM
    				AgendaBundle:Cupo c    			
    			JOIN
    				c.paciente p
				JOIN
    				c.agenda a		    			
				JOIN
    				a.sede s				
    			WHERE
			    	c.hora > :inicio AND
			    	c.hora <= :fin
			    	".$con_sede."
			    GROUP BY c.estado, p.id ORDER BY p.id ASC";
	
		$query = $em->createQuery($dql);
	
		$query->setParameter('inicio', $desde);
		$query->setParameter('fin', $hasta);
			
		return $query->getResult();
	}
	
	public function findInformeAgenda($con_sede,$desde,$hasta)
	{
		$em = $this->getEntityManager();
		$dql= " SELECT
					COUNT(c.estado) AS cantidad,										
					c.estado,
					a.nota as agenda,
					a.fechaInicio,
					s.nombre as sede
    			FROM
    				AgendaBundle:Cupo c
    			JOIN
    				c.cargo car
    			JOIN
    				c.paciente p
    			JOIN
    				c.agenda a
				JOIN
    				a.sede s
    			WHERE
			    	c.hora > :inicio AND
			    	c.hora <= :fin
			    	".$con_sede."
			    GROUP BY c.estado, a.id ORDER BY c.estado ASC";
	
		$query = $em->createQuery($dql);
	
		$query->setParameter('inicio', $desde);
		$query->setParameter('fin', $hasta);
			
		return $query->getResult();
	}

}
