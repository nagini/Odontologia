<?php
namespace dlaser\ParametrizarBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class FacturaRepository extends EntityRepository{
	
	function findCheckExm($paciente,$cargo){
		
		$em = $this->getEntityManager();		
		$ultimaCx = $em->createQuery('SELECT
											f.id,
											f.fecha
										FROM
											ParametrizarBundle:Factura f
										WHERE
											f.paciente = :paciente AND
											f.cargo = :cargo
										ORDER BY 
											f.fecha DESC');
				
		$ultimaCx->setParameter('paciente', $paciente);
		$ultimaCx->setParameter('cargo', $cargo);		
		return $ultimaCx->getArrayResult();
	}
	
	function findExaInCcv($examen){
		
		$em = $this->getEntityManager();		
		$dql = $em->createQuery('SELECT
									f.id
								FROM
									ParametrizarBundle:Factura f
								JOIN
									f.cargo c
								WHERE
									f.fecha > :fecha AND
									c.cups = :codigo');
		
		$dql->setParameter('codigo', $examen['codigo']);
		$dql->setParameter('fecha', $examen['fecha']);		
		return $dql->getArrayResult();
	}
	
	function findCxAnt($paciente,$cargo){
		
		$em = $this->getEntityManager();
		$ultimaCx = $em->createQuery('SELECT
										f.id,
										f.fecha
									FROM
										ParametrizarBundle:Factura f
									WHERE
										f.paciente = :paciente AND
										f.cargo = :cargo
									ORDER BY f.fecha DESC');
		
		$ultimaCx->setParameter('paciente', $paciente);
		$ultimaCx->setParameter('cargo', $cargo);		
		return $ultimaCx->getArrayResult();
	}
}