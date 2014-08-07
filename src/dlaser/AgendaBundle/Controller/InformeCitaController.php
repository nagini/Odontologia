<?php
namespace dlaser\AgendaBundle\Controller;

use dlaser\ParametrizarBundle\Entity\Cargo;
use dlaser\ParametrizarBundle\Entity\Paciente;
use Doctrine\Tests\Common\Annotations\Null;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;





class InformeCitaController extends Controller
{
	public function informeTipoAction()
	{
		$em = $this->getDoctrine()->getEntityManager();
		
		$sedes = $em->getRepository("ParametrizarBundle:Sede")->findAll();		
		 
		$breadcrumbs = $this->get("white_october_breadcrumbs");
		$breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
		$breadcrumbs->addItem("Informe Cupos");
			
		return $this->render('AgendaBundle:InformeCita:informe_tipo.html.twig', array(
				'sedes' => $sedes,
				
		));
	}
	
	public function informePacienteAction($paciente)
	{
		$em = $this->getDoctrine()->getEntityManager();    
        $paciente = $em->getRepository('ParametrizarBundle:Paciente')->find($paciente);        
        
    
        if (!$paciente) {
            throw $this->createNotFoundException('El paciente solicitado no existe.');
        }
        
        $query = $em->createQuery(' SELECT        							
				                    c.hora,
				                    c.estado,
				                    c.nota,
				                    car.nombre
				                    FROM AgendaBundle:Cupo c
				                    LEFT JOIN c.paciente p
				                    LEFT JOIN c.cargo car
				                    WHERE p.id = :paciente and c.hora < :fecha
				                    ORDER BY c.hora DESC, c.estado ASC');
        
        
        $query->setParameter('paciente', $paciente->getId());
        $fecha = new \DateTime('now');
        $query->setParameter('fecha', $fecha->format('Y-m-d 00:00:00'));
        $cupo = $query->getArrayResult();
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Informe de pacientes");
        
        $html = $this->render('AgendaBundle:InformeCita:informe_personal_pacientes.html.twig', array(
        		'entity' => $cupo,
        		'paciente' => $paciente
        ));          
        
        return $html;
	}
	
	public function informeCitasAction()
	{	
		$request = $this->get('request');
	
		$sede = $request->request->get('sede');
		$option = $request->request->get('tipo');		
		$dateStart = date_create_from_format('d/m/Y H:i:s',$request->request->get('f_inicio').' 00:00:00');
		$dateEnd   = date_create_from_format('d/m/Y H:i:s',$request->request->get('f_fin').' 23:59:00');	
		
		
		if($dateStart > $dateEnd )
		{
			$this->get('session')->setFlash('info', 'Las Fechas No Son Correctas, Vuelva A Ingresar La Información.');
			return $this->redirect($this->generateUrl('factura_reporte_medico'));
		}	
	
		$em = $this->getDoctrine()->getEntityManager();
	
		if(is_numeric(trim($sede))){
			$obj_sede = $em->getRepository("ParametrizarBundle:Sede")->find($sede);
		}else{
			$obj_sede['nombre'] = 'Todas las sedes.';
			$obj_sede['id'] = '';
		}		
	
		if(is_object($obj_sede)){
			$con_sede = "AND a.sede =".$sede;
		}else{
			$con_sede = "";
		}	
		
		
		$breadcrumbs = $this->get("white_october_breadcrumbs");
		$breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
		$breadcrumbs->addItem("Informe Cupos");
		
		switch ($option)
		{
			case 'A':
				$entity = $em->getRepository('AgendaBundle:Cupo')->findInformeAgenda($con_sede,$dateStart,$dateEnd);
				$html = "informe_por_agenda.html.twig";
				break;
			case 'P':
				$entity = $em->getRepository('AgendaBundle:Cupo')->findInformePaciente($con_sede,$dateStart,$dateEnd);
				$html = "informe_por_paciente.html.twig";
				break;
			case 'C':
				$entity = $em->getRepository('AgendaBundle:Cupo')->findInformeCargo($con_sede,$dateStart,$dateEnd);
				$html = "informe_por_cargo.html.twig";
				break;
			case 'G':
				$entity = $em->getRepository('AgendaBundle:Cupo')->findInformeGeneral($con_sede,$dateStart,$dateEnd);
				$html = "informe_general.html.twig";
				break;
		}		
	
		return $this->render('AgendaBundle:InformeCita:'.$html, array('entities' => $entity,));
	}
}