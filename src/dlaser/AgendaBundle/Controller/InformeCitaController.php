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
        $sede  = $em->getRepository('ParametrizarBundle:Sede')->find(4);
    
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
				                    ORDER BY c.hora ASC, c.estado ASC');
        
        
        $query->setParameter('paciente', $paciente->getId());
        $fecha = new \DateTime('now');
        $query->setParameter('fecha', $fecha->format('Y-m-d 00:00:00'));
        $cupo = $query->getArrayResult();
        
        $html = $this->renderView('AgendaBundle:InformeCita:informe_pacientes.pdf.twig', array(
        		'entity' => $cupo,
        		'paciente' => $paciente
        ));        
        
        $this->get('io_tcpdf')->dir = $sede->getDireccion();
        $this->get('io_tcpdf')->ciudad = $sede->getCiudad();
        $this->get('io_tcpdf')->tel = $sede->getTelefono();
        $this->get('io_tcpdf')->mov = $sede->getMovil();
        $this->get('io_tcpdf')->mail = $sede->getEmail();
        $this->get('io_tcpdf')->sede = $sede->getnombre();
        $this->get('io_tcpdf')->empresa = $sede->getEmpresa()->getNombre();
        
        $this->get('io_tcpdf')->SetMargins(3, 10, 3);
        
        return $this->get('io_tcpdf')->quick_pdf($html, 'Informe de citas'.$sede->getNombre().'.pdf', 'I');		
	}
}