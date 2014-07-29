<?php

namespace dlaser\AdminBundle\Controller;;

use Io\TcpdfBundle\Helper\Tcpdf;

use dlaser\ParametrizarBundle\Entity\Facturacion;

use dlaser\AdminBundle\Form\AdmisionAuxType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use dlaser\ParametrizarBundle\Entity\Factura;
use dlaser\AdminBundle\Form\FacturaType;
use dlaser\AdminBundle\Form\FacturacionType;
use dlaser\AdminBundle\Form\AdmisionType;
use dlaser\HcBundle\Entity\Hc;
use dlaser\HcBundle\Entity\HcMedicamento;

class FacturaController extends Controller
{
    
    public function newAction($cupo)
    {
        $entity = new Factura();
        $form   = $this->createForm(new FacturaType(), $entity);
        
        $em = $this->getDoctrine()->getEntityManager();
        $reserva = $em->getRepository('AgendaBundle:Cupo')->find($cupo);
        $contrato = $em->getRepository('ParametrizarBundle:Contrato')->findOneBy(array('sede' => $reserva->getAgenda()->getSede()->getId(), 'cliente' => $reserva->getCliente()));
        $actividad = $em->getRepository('ParametrizarBundle:Actividad')->findOneBy(array('cargo' => $reserva->getCargo()->getId(), 'contrato' => $contrato->getId()));
        $cliente = $em->getRepository('ParametrizarBundle:Cliente')->find($reserva->getCliente());
        $valorFijo = $actividad->getPrecio();

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Factura", $this->get("router")->generate("factura_search"));
        $breadcrumbs->addItem("Nueva admisión");
        
        return $this->render('AdminBundle:Factura:new.html.twig', array(
            'cupo' => $reserva,
        	'cliente' => $cliente,
            'actividad' => $valorFijo,
        	'contrato' => $contrato,
            'form'   => $form->createView()
        ));
    }
    
    public function saveAction($cupo)
    {
        $entity  = new Factura();
        
        /*$request = $this->getRequest();
        $form    = $this->createForm(new FacturaType(), $entity);
        $form->bindRequest($request);*/
        
       // if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getEntityManager();
            $reserva = $em->getRepository('AgendaBundle:Cupo')->find($cupo);
            
            if (!$reserva) {
                throw $this->createNotFoundException('La reserva solicitada no existe');
            }
            
            $cliente = $em->getRepository('ParametrizarBundle:Cliente')->find($reserva->getCliente());
            $contrato = $em->getRepository('ParametrizarBundle:Contrato')->findOneBy(array('sede' => $reserva->getAgenda()->getSede()->getId(), 'cliente' => $reserva->getCliente()));
            $actividad = $em->getRepository('ParametrizarBundle:Actividad')->findOneBy(array('cargo' => $reserva->getCargo()->getId(), 'contrato' => $contrato->getId()));
            
            if($actividad->getPrecio()){
                $valor = $actividad->getPrecio();
            }else{
                $valor = round(($reserva->getCargo()->getValor()+($reserva->getCargo()->getValor()*$contrato->getPorcentaje())));
            }

            $entity->setFecha(new \DateTime('now'));
            $entity->setEstado('P');
            $entity->setCargo($reserva->getCargo());
            $entity->setValor($valor);
            $entity->setCliente($cliente);
            $entity->setPaciente($reserva->getPaciente());
            $entity->setSede($reserva->getAgenda()->getSede());
            $entity->setCupo($reserva);
            
            $entity->setAutorizacion('AB'.$cupo);
            $entity->setCopago(0);            
            
            $reserva->setEstado('F');
            
            $em->persist($entity);
            $em->persist($reserva);
            $em->flush();
            
            $this->get('session')->setFlash('ok', 'La admisión ha sido registrada éxitosamente.');            

                  
        /*
          }
        
        return $this->render('AdminBundle:Factura:new.html.twig', array(
                'cupo' => $reserva,
                'actividad' => $valor,
                'form'   => $form->createView()
        ));*/
        
        return $this->render('AdminBundle:Factura:search.html.twig');
    }   
        
    public function searchAction()
    {
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
    	$breadcrumbs->addItem("Factura", $this->get("router")->generate("factura_search"));
    	$breadcrumbs->addItem("Buscar");
    	
    	return $this->render('AdminBundle:Factura:search.html.twig');
    }
    
    public function buscarFacturaAction()
    {
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("factura_arqueo"));
    	$breadcrumbs->addItem("Factura", $this->get("router")->generate("factura_search"));
    	$breadcrumbs->addItem("Buscar admisión");    	
    	
    	return $this->render('AdminBundle:Factura:buscar_factura.html.twig');
    }
    
    public function listAdmisionAction()
    {
    	$request = $this->get('request');

    	$parametro = $request->request->get('parametro');
    	$valor = $request->request->get('valor');

    	if(is_numeric(trim($valor)) && $valor > 0 ){

    		$em = $this->getDoctrine()->getEntityManager();

    		$dql= " SELECT
			    		f.id,
			    		p.id as paciente,
			    		p.tipoId,
			    		p.identificacion,
			    		f.fecha,
			    		f.autorizacion,
			    		p.priNombre,
			    		p.segNombre,
			    		p.priApellido,
			    		p.segApellido,
			    		c.cups,
			    		f.valor,
			    		f.copago,
			    		f.descuento,
			    		f.estado
		    		FROM
		    			ParametrizarBundle:Factura f
		    		JOIN
		    			f.cargo c
		    		JOIN
		    			f.paciente p
		    		JOIN
		    			f.cliente cli
		    		WHERE
			    		p.identificacion = :identificacion
		    		ORDER BY
		    			f.fecha DESC";
    		
    		$query = $em->createQuery($dql);
    		$query->setParameter('identificacion', $valor);    		
    		$entity = $query->getResult();
    		
    		$breadcrumbs = $this->get("white_october_breadcrumbs");
    		$breadcrumbs->addItem("Inicio", $this->get("router")->generate("factura_arqueo"));
    		$breadcrumbs->addItem("Factura", $this->get("router")->generate("factura_search"));    		
    		$breadcrumbs->addItem("Listar admisión");
    		
    		return $this->render('AdminBundle:Factura:listar_admisiones.html.twig', array(
    				'entities' => $entity
    		));

    	}else{
    		$this->get('session')->setFlash('info', 'El parametro ingresado es incorrecto.');
    		return $this->redirect($this->generateUrl('factura_admision_search'));
    	}
    }

    public function editAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();    
    	$entity = $em->getRepository('ParametrizarBundle:Factura')->find($id);
    
    	if (!$entity) {
    		throw $this->createNotFoundException('La factura solicitada no existe');
    	}
    	$user = $this->get('security.context')->getToken()->getUser();
    	    
    	if ($user->getPerfil() == 'ROLE_ADMIN') {
    		$editForm = $this->createForm(new AdmisionType(), $entity);
    	}
    	else{
    		$editForm = $this->createForm(new AdmisionAuxType(), $entity);
    	}
    
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
    	$breadcrumbs->addItem("Factura", $this->get("router")->generate("factura_search"));
    	$breadcrumbs->addItem("Detalle ",$this->get("router")->generate("factura_show",array("id" => $id)));
    	$breadcrumbs->addItem("Modificar admisión");
    	
    	return $this->render('AdminBundle:Factura:edit.html.twig', array(
    			'entity'      => $entity,
    			'edit_form'   => $editForm->createView(),
    	));
    }
    
    public function updateAction($id)
        {
    	$em = $this->getDoctrine()->getEntityManager();    
    	$entity = $em->getRepository('ParametrizarBundle:Factura')->find($id);
    	
    	if (!$entity) {
    		throw $this->createNotFoundException('La factura solicitada no existe.');
    	}
    	$request = $this->getRequest();
    	$user = $this->get('security.context')->getToken()->getUser();
    	if($user->getPerfil() == 'ROLE_ADMIN'){    		
    		$editForm = $this->createForm(new AdmisionType(), $entity);    		
    	}else{
    		$editForm = $this->createForm(new AdmisionAuxType(), $entity);
    	}    	
    	$editForm->bindRequest($request);
    	
    
    	if ($editForm->isValid()) {
    		
    		$cliente = $em->getRepository('ParametrizarBundle:Cliente')->find($entity->getCliente()->getId());    	
    		$contrato = $em->getRepository('ParametrizarBundle:Contrato')->findOneBy(array('sede' => $entity->getSede()->getId(), 'cliente' => $cliente->getId()));
    		
    		if(!$contrato){
    			$this->get('session')->setFlash('info', 'El cliente seleccionado no tiene contrato con la sede, por favor verifique y vuelva a intentarlo.');
    			return $this->redirect($this->generateUrl('factura_edit', array('id' => $id)));
    		} 		
    		
    		$actividad = $em->getRepository('ParametrizarBundle:Actividad')->findOneBy(array('cargo' => $entity->getCargo()->getId(), 'contrato' => $contrato->getId()));
    		
    		if(!$user->getPerfil() == 'ROLE_ADMIN'){    		
	    		if($actividad->getPrecio()){
	    			$valor = $actividad->getPrecio();
	    		}else{
	    			$valor = round(($entity->getCargo()->getValor()+($entity->getCargo()->getValor()*$contrato->getPorcentaje()/100)));
	    		}
	    		
	    		$entity->setValor($valor);
    		}    		
    		$entity->setCliente($cliente);
    
    		$em->persist($entity);
    		$em->flush();
    
    		$this->get('session')->setFlash('info', 'La información de la admisión ha sido modificada éxitosamente.');    
    		return $this->redirect($this->generateUrl('factura_edit', array('id' => $id)));
    	}    
    	return $this->render('AdminBundle:Factura:edit.html.twig', array(
    			'entity'      => $entity,
    			'edit_form'   => $editForm->createView(),
    	));
    }   
    
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
    
        $factura = $em->getRepository('ParametrizarBundle:Factura')->find($id);
        
        
        if (!$factura) {
            throw $this->createNotFoundException('La factura solicitada no esta disponible.');
        }
    
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Factura", $this->get("router")->generate("factura_search"));
        $breadcrumbs->addItem("Detalle admisión");
        
        return $this->render('AdminBundle:Factura:show.html.twig', array(
                'entity'  => $factura,
                
        ));
    }
    
	public function imprimirAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entity = $em->getRepository('ParametrizarBundle:Factura')->find($id);
    	
    	if (!$entity) {
    		throw $this->createNotFoundException('La factura a imprimir no esta disponible.');
    	}    
    	
    	$paciente = $entity->getPaciente();
    	$cliente = $entity->getCliente();
    	$sede = $entity->getSede();
    		
    	$html = $this->renderView('AdminBundle:Factura:factura.pdf.twig',
    			array('entity' 	=> $entity,    				  
    				  'paciente' => $paciente,
    				  'cliente'	=> $cliente,
    				  'sede'=>$sede
    			));
    	
    	
    	$this->get('io_tcpdf')->dir = $sede->getDireccion();
    	$this->get('io_tcpdf')->ciudad = $sede->getCiudad();
    	$this->get('io_tcpdf')->tel = $sede->getTelefono();
    	$this->get('io_tcpdf')->mov = $sede->getMovil();
    	$this->get('io_tcpdf')->mail = $sede->getEmail();
    	$this->get('io_tcpdf')->sede = $sede->getnombre();
    	$this->get('io_tcpdf')->empresa = $sede->getEmpresa()->getNombre();

    	
    	

    	return $this->get('io_tcpdf')->quick_pdf($html, 'factura.pdf', 'I');
	}
    
    public function updateEstadoAction($id, $estado)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entity = $em->getRepository('ParametrizarBundle:Factura')->find($id);
    
    	if (!$entity) {
    		throw $this->createNotFoundException('La factura solicitada no existe.');
    	}
    	
    	$entity->setEstado($estado);

    	$em->persist($entity);
    	$em->flush();
    
    	return new Response('ok');
    }    
    
    /**
     * @uses Función que devuelve las sedes asociadas de un usuario. 
     * 
     * @param ninguno
     */
    public function ajaxBuscarAction() {
        
        $request = $this->get('request');
        $parametro=$request->request->get('parametro');
        $valor=$request->request->get('valor');
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $fecha=new \DateTime('now');
        
        if($parametro == 'codigo'){
            $query = $em->createQuery(' SELECT c.id,
                    c.hora,
                    c.nota,
                    c.registra,
                    c.verificacion,
                    p.priNombre,
                    p.segNombre,
                    p.priApellido,
                    p.segApellido,
            		p.movil,
                    car.nombre
                    FROM AgendaBundle:Cupo c
                    LEFT JOIN c.paciente p
                    LEFT JOIN c.cargo car
                    WHERE c.verificacion = :codigo
                    AND c.hora >= :fecha
                    ORDER BY c.hora ASC');           
            
            $query->setParameter('fecha', $fecha->format('Y-m-d 00:00:00'));
            $query->setParameter('codigo', $valor);
            $reserva = $query->getArrayResult();
        }
        
        if($parametro == 'identificacion'){
        
            $query = $em->createQuery(' SELECT c.id,
                    c.hora,
                    c.nota,
                    c.registra,
                    c.verificacion,
                    p.priNombre,
                    p.segNombre,
                    p.priApellido,
                    p.segApellido,
            		p.movil,
                    car.nombre as cargo,
            		s.nombre as sede
                    FROM AgendaBundle:Cupo c
                    LEFT JOIN c.paciente p
                    LEFT JOIN c.cargo car
            		LEFT JOIN c.agenda a
            		LEFT JOIN a.sede s
                    WHERE p.identificacion = :identificacion
                    AND c.hora >= :fechaI
            		AND c.hora <= :fechaF
            		AND c.estado = :estado
                    ORDER BY c.hora ASC');
            
            
            $query->setParameter('fechaI', $fecha->format('Y-m-d 00:00:00'));
            $query->setParameter('fechaF', $fecha->format('Y-m-d 23:59:00'));
            $query->setParameter('identificacion', $valor);
            $query->setParameter('estado', 'A');
            $reserva = $query->getArrayResult();
    }  
        
            if (!$reserva){
                $response=array("responseCode"=>400, "msg"=>"No existen reservas para los parametros de consulta ingrasados.");
            }else{           
    
                $response=array("responseCode"=>200);
    
                foreach($reserva as $key => $value)
                {
                    $response['cupo'][$key] = $value;
                }
                
            }
        
        $return=json_encode($response);
        return new Response($return,200,array('Content-Type'=>'application/json'));
        
    } 

    public function arqueoAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();    	
    	$usuario = $this->get('security.context')->getToken()->getUser();
    	$usuario = $em->getRepository('UsuarioBundle:Usuario')->find($usuario->getId());
    	$sedes = $usuario->getSede();
    	
    	if(!$usuario)
    	{
    		throw $this->createNotFoundException('El usuario no existe no esta identificado.'); 
    	}    	
    	
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("factura_arqueo"));
    	$breadcrumbs->addItem("Factura", $this->get("router")->generate("factura_search"));
    	$breadcrumbs->addItem("Arqueo");
    	
    	return $this->render('AdminBundle:Factura:arqueo.html.twig', array(
    			'sedes' => $sedes,
    			'usuario' => $sedes
    	));
    	
    }
    
    public function imprimirArqueoAction($sede)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$fecha=new \DateTime('now');
    	
    	$dql= " SELECT 
    				f.id,
    				c.cups,
    				f.autorizacion,
    				p.identificacion,
    				p.priNombre,
                    p.segNombre,
                    p.priApellido,
                    p.segApellido,
                    cli.id as cliente,
                    cli.nombre,
                    f.valor,
                    f.copago,
                    f.descuento,
                    f.estado
    			FROM 
    				ParametrizarBundle:Factura f
    			JOIN
    				f.cargo c
    			JOIN
    				f.paciente p
    			JOIN
    				f.cliente cli
    			WHERE 
    				f.fecha > :inicio AND 
    				f.fecha <= :fin AND 
    				f.sede = :id 
    			ORDER BY 
    				f.fecha ASC";
    	
    	$query = $em->createQuery($dql);

    	$query->setParameter('inicio', $fecha->format('Y-m-d 00:00:00'));
    	$query->setParameter('fin', $fecha->format('Y-m-d 23:59:00'));
    	$query->setParameter('id', $sede);
    	
    	$entity = $query->getResult();
    	 
    	$sede  = $em->getRepository('ParametrizarBundle:Sede')->find($sede);    	 
    	
    	if (!$sede) {
    		throw $this->createNotFoundException('La sede no existe no esta identificado.');
    	}    	
    	if (!$entity) {
    		$this->get('session')->setFlash('info', 'No hay informacion facturada el dia de hoy.');
    		return $this->redirect($this->generateUrl('factura_arqueo'));
    	}
    	
    	$html = $this->renderView('AdminBundle:Factura:imprimir_arqueo.pdf.twig', array(
    			'entity' => $entity,
    			'sede' => $sede
    	));
    	
    	$this->get('io_tcpdf')->dir = $sede->getDireccion();
    	$this->get('io_tcpdf')->ciudad = $sede->getCiudad();
    	$this->get('io_tcpdf')->tel = $sede->getTelefono();
    	$this->get('io_tcpdf')->mov = $sede->getMovil();
    	$this->get('io_tcpdf')->mail = $sede->getEmail();
    	$this->get('io_tcpdf')->sede = $sede->getnombre();
    	$this->get('io_tcpdf')->empresa = $sede->getEmpresa()->getNombre();
    	
    	$this->get('io_tcpdf')->SetMargins(3, 10, 3);
    	 
    	return $this->get('io_tcpdf')->quick_pdf($html, 'Arqueo de Caja '.$fecha->format('d-m-Y').' Sede '.$sede->getNombre().'.pdf', 'I');
    }
    
    
    
    public function listadoClienteAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$sedes = $em->getRepository("ParametrizarBundle:Sede")->findAll();
    	$clientes = $em->getRepository("ParametrizarBundle:Cliente")->findAll();
    	
    	if($this->get('security.context')->isGranted('ROLE_ADMIN')) {
    		$plantilla = 'AdminBundle:Factura:listar_cliente.html.twig';
    	}else {
    		$plantilla = 'AdminBundle:Reporte:listado_cliente.html.twig';
    	}
    	
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("factura_arqueo"));
    	$breadcrumbs->addItem("Factura", $this->get("router")->generate("factura_search"));
    	$breadcrumbs->addItem("Clientes");

    	return $this->render($plantilla, array(
    			'sedes' => $sedes,
    			'clientes' => $clientes
    	));
    }
    
    /**
     * @uses Muestra el listado generado a partir de los parametros de consultas definidos.
     * 
     * @param Pasados por POST.
     */
    public function actividadesClienteAction()
    {
    	
    	$request = $this->get('request');
    	
    	$sede = $request->request->get('sede');
    	$cliente = $request->request->get('cliente');
    	$f_inicio = $request->request->get('f_inicio');
    	$f_fin = $request->request->get('f_fin');
    	
    	if(trim($f_inicio)){
    		$desde = explode('/',$f_inicio);
    		
    		if(!checkdate($desde[1],$desde[0],$desde[2])){		
    			$this->get('session')->setFlash('info', 'La fecha de inicio ingresada es incorrecta.');
    			return $this->redirect($this->generateUrl('factura_cliente_list'));
    		}
    	}else{
    		$this->get('session')->setFlash('info', 'La fecha de inicio no puede estar en blanco.');
    		return $this->redirect($this->generateUrl('factura_cliente_list'));
    	}
    	
    	if(trim($f_fin)){
    		$hasta = explode('/',$f_fin);
    		
    		if(!checkdate($hasta[1],$hasta[0],$hasta[2])){		
    			$this->get('session')->setFlash('info', 'La fecha de finalización ingresada es incorrecta.');
    			return $this->redirect($this->generateUrl('factura_cliente_list'));
    		}
    	}else{
    		$this->get('session')->setFlash('info', 'La fecha de finalización no puede estar en blanco.');
    		return $this->redirect($this->generateUrl('factura_cliente_list'));
    	}
    	    	  	
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	if(is_numeric(trim($sede))){
    		$obj_sede = $em->getRepository("ParametrizarBundle:Sede")->find($sede);
    	}else{
    		$obj_sede['nombre'] = 'Todas las sedes.';
    		$obj_sede['id'] = '';
    	}
    	
    	if(is_numeric(trim($cliente))){
    		$obj_cliente = $em->getRepository("ParametrizarBundle:Cliente")->find($cliente);
    	}else{
    		$this->get('session')->setFlash('info', 'Debe seleccionar un cliente para continuar..');
    		return $this->redirect($this->generateUrl('factura_cliente_list'));
    	}
    	
    	if(!$obj_cliente){
    		$this->get('session')->setFlash('info', 'El cliente seleccionado no existe.');
    		return $this->redirect($this->generateUrl('factura_cliente_list'));
    	}
    	
    	if(is_object($obj_sede)){
    		$con_sede = "AND f.sede =".$sede;
    	}else{
    		$con_sede = "";
    	}
    	
    	$dql= " SELECT
			    	f.id,
			    	p.id as paciente,
			    	p.tipoId,
			    	p.identificacion,
			    	f.fecha,
			    	f.autorizacion,
			    	p.priNombre,
			    	p.segNombre,
			    	p.priApellido,
			    	p.segApellido,
			    	c.cups,
			    	f.valor,
			    	f.copago,
			    	f.descuento,
			    	f.estado
    			FROM
    				ParametrizarBundle:Factura f
    			JOIN
    				f.cargo c
    			JOIN
    				f.paciente p
    			JOIN
    				f.cliente cli
    			WHERE
			    	f.fecha > :inicio AND
			    	f.fecha <= :fin AND			    	
			    	f.cliente = :cliente ".
			    	$con_sede."
		    	ORDER BY
		    		f.fecha ASC";
    	 
    	$query = $em->createQuery($dql);
    	
    	$query->setParameter('inicio', $desde[2]."/".$desde[1]."/".$desde[0].' 00:00:00');
    	$query->setParameter('fin', $hasta[2]."/".$hasta[1]."/".$hasta[0].' 23:59:00');
    	$query->setParameter('cliente', $cliente);
    	
    	$entity = $query->getResult();
    	
    	$user = $this->get('security.context')->getToken()->getUser();
    	
    	if($user->getPerfil() == 'ROLE_ADMIN') {
    		$plantilla = 'AdminBundle:Factura:actividades_cliente.html.twig';
    	}else {
    		$plantilla = 'AdminBundle:Reporte:actividades_cliente.html.twig';
    	}
    	
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("factura_arqueo"));
    	$breadcrumbs->addItem("Factura", $this->get("router")->generate("factura_search"));
    	$breadcrumbs->addItem("Actividades");
    	    	
    	return $this->render($plantilla, array(
    			'entities' => $entity,
    			'sede' => $obj_sede,
    			'cliente' => $obj_cliente,
    			'f_i' => $desde[2]."/".$desde[1]."/".$desde[0],
    			'f_f' => $hasta[2]."/".$hasta[1]."/".$hasta[0]
    	));
    }
    
    
    public function listadoMedicoAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	 
    	$sedes = $em->getRepository("ParametrizarBundle:Sede")->findAll();
    	$medicos = $em->getRepository("UsuarioBundle:Usuario")->findBy(array('perfil' => 'ROLE_MEDICO'), array('nombre' => 'ASC'));
    	
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));    	
    	$breadcrumbs->addItem("Reporte medico");
    	    
    	return $this->render('AdminBundle:Reporte:listado_medico.html.twig', array(
    			'sedes' => $sedes,
    			'medicos' => $medicos
    	));
    }
    
    /**
     * @uses Muestra el listado generado a partir de los parametros de consultas definidos.
     *
     * @param Pasados por POST.
     */
 public function actividadesMedicoAction()
    {
    	 
    	$request = $this->get('request');
    	 
    	$sede = $request->request->get('sede');
    	$usuario = $request->request->get('usuario');
    	$f_inicio = $request->request->get('f_inicio');
    	$f_fin = $request->request->get('f_fin');
    	
    	$url_retorno = 'factura_reporte_medico';
    	 
    	if(trim($f_inicio)){
    		$desde = explode('/',$f_inicio);
    
    		if(!checkdate($desde[1],$desde[0],$desde[2])){
    			$this->get('session')->setFlash('info', 'La fecha de inicio ingresada es incorrecta.');
    			return $this->redirect($this->generateUrl($url_retorno));
    		}
    	}else{
    		$this->get('session')->setFlash('info', 'La fecha de inicio no puede estar en blanco.');
    		return $this->redirect($this->generateUrl($url_retorno));
    	}
    	 
    	if(trim($f_fin)){
    		$hasta = explode('/',$f_fin);
    
    		if(!checkdate($hasta[1],$hasta[0],$hasta[2])){
    			$this->get('session')->setFlash('info', 'La fecha de finalización ingresada es incorrecta.');
    			return $this->redirect($this->generateUrl($url_retorno));
    		}
    	}else{
    		$this->get('session')->setFlash('info', 'La fecha de finalización no puede estar en blanco.');
    		return $this->redirect($this->generateUrl($url_retorno));
    	}
    	 
    	$em = $this->getDoctrine()->getEntityManager();
    	 
    	if(is_numeric(trim($sede))){
    		$obj_sede = $em->getRepository("ParametrizarBundle:Sede")->find($sede);
    	}else{
    		$obj_sede['nombre'] = 'Todas las sedes.';
    		$obj_sede['id'] = '';
    	}
    	 
    	if(is_numeric(trim($usuario))){
    		$obj_usuario = $em->getRepository("UsuarioBundle:Usuario")->find($usuario);
    	}else{
    		$this->get('session')->setFlash('info', 'Debe seleccionar un medico para continuar..');
    		return $this->redirect($this->generateUrl($url_retorno));
    	}
    	 
    	if(!$obj_usuario){
    		$this->get('session')->setFlash('info', 'El medico seleccionado no existe.');
    		return $this->redirect($this->generateUrl($url_retorno));
    	}
    	 
    	if(is_object($obj_sede)){
    		$con_sede = "AND f.sede =".$sede;
    	}else{
    		$con_sede = "";
    	}
    	 
    	$dql= " SELECT
			    	f.id,
			    	p.id as paciente,
			    	p.tipoId,
			    	p.identificacion,
			    	f.fecha,
			    	f.autorizacion,
			    	p.priNombre,
			    	p.segNombre,
			    	p.priApellido,
			    	p.segApellido,
			    	c.cups,
			    	f.valor,
			    	f.copago,
			    	f.descuento,
			    	f.estado
    			FROM
    				ParametrizarBundle:Factura f
    			JOIN
    				f.cargo c
    			JOIN
    				f.paciente p
    			JOIN
    				f.cupo cp
    			JOIN
    				cp.agenda a
    			WHERE
			    	f.fecha > :inicio AND
			    	f.fecha <= :fin AND
			    	a.usuario = :usuario ".
    			    	$con_sede."
		    	ORDER BY
		    		f.fecha ASC";
    
    	$query = $em->createQuery($dql);
    	 
    	$query->setParameter('inicio', $desde[2]."/".$desde[1]."/".$desde[0].' 00:00:00');
    	$query->setParameter('fin', $hasta[2]."/".$hasta[1]."/".$hasta[0].' 23:59:00');
    	$query->setParameter('usuario', $usuario);
    	 
    	$entity = $query->getResult();
    	
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));    	
    	$breadcrumbs->addItem("Reporte ",$this->get("router")->generate("factura_reporte_medico"));
    	$breadcrumbs->addItem("Actividad medico");
    
    	return $this->render('AdminBundle:Reporte:actividades_medico.html.twig', array(
    			'entities' => $entity,
    			'sede' => $obj_sede,
    			'usuario' => $obj_usuario,
    			'f_i' => $desde[2]."/".$desde[1]."/".$desde[0],
    			'f_f' => $hasta[2]."/".$hasta[1]."/".$hasta[0]
    	));
    }
    
    
    public function imprimirHonorarioAction()
    {
    	$request = $this->get('request');
    	 
    	$sede = $request->request->get('sede');
    	$usuario = $request->request->get('usuario');
    	$f_inicio = $request->request->get('f_inicio');
    	$f_fin = $request->request->get('f_fin');
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	 
    	if(is_numeric(trim($usuario))){
    		$obj_usuario = $em->getRepository("UsuarioBundle:Usuario")->find($usuario);
    	}else{
    		$this->get('session')->setFlash('info', 'Debe seleccionar un medico para continuar..');
    		return $this->redirect($this->generateUrl('factura_reporte_medico'));
    	}
    	 
    	if(!$obj_usuario){
    		$this->get('session')->setFlash('info', 'El medico seleccionado no existe.');
    		return $this->redirect($this->generateUrl('factura_reporte_medico'));
    	}
    	 
    	$obj_sede = null;
    	
    	if(is_numeric(trim($sede))){
    		$obj_sede = $em->getRepository("ParametrizarBundle:Sede")->find($sede);
    		$con_sede = "AND f.sede =".$sede;
    		$empresa = $obj_sede->getEmpresa();
    	}else{
    		$obj_sede = $em->getRepository("ParametrizarBundle:Sede")->find('1');
    		$empresa = $obj_sede->getEmpresa();
    		$con_sede = "";
    	}
    	 
    	$dql= " SELECT
			    	f.id,
			    	p.id as paciente,
			    	p.tipoId,
			    	p.identificacion,
			    	f.fecha,
			    	f.autorizacion,
    				cli.nombre,
			    	p.priNombre,
			    	p.segNombre,
			    	p.priApellido,
			    	p.segApellido,
			    	c.cups
    			FROM
    				ParametrizarBundle:Factura f
    			JOIN
    				f.cargo c
    			JOIN
    				f.paciente p
    			JOIN
    				f.cliente cli
    			JOIN
    				f.cupo cp
    			JOIN
    				cp.agenda a
    			WHERE
			    	f.fecha > :inicio AND
			    	f.fecha <= :fin AND
    				f.estado = :estado AND
			    	a.usuario = :usuario ".
    			    	$con_sede."
		    	ORDER BY
		    		f.fecha ASC";
    	  
    	$query = $em->createQuery($dql);
    	 
    	$query->setParameter('inicio', $f_inicio.' 00:00:00');
    	$query->setParameter('fin', $f_fin.' 23:59:00');
    	$query->setParameter('usuario', $usuario);
    	$query->setParameter('estado', 'I');
    	 
    	$entity = $query->getResult();
    	
    	$dql= " SELECT
			    	c.cups,
    				count(c.id) as cantidad
    			FROM
    				ParametrizarBundle:Factura f
    			JOIN
    				f.cargo c
    			JOIN
    				f.cupo cp
    			JOIN
    				cp.agenda a
    			WHERE
			    	f.fecha > :inicio AND
			    	f.fecha <= :fin AND
    				f.estado = :estado AND
			    	a.usuario = :usuario ".
    				    	$con_sede."
    			GROUP BY
    				c.cups
		    	ORDER BY
		    		f.fecha ASC
    			";
    	
    	$query = $em->createQuery($dql);
    	 
    	$query->setParameter('inicio', $f_inicio.' 00:00:00');
    	$query->setParameter('fin', $f_fin.' 23:59:00');
    	$query->setParameter('usuario', $usuario);
    	$query->setParameter('estado', 'I');
    	 
    	$cantidad = $query->getResult();   	
    
    	$html = $this->renderView('AdminBundle:Reporte:honorarios.pdf.twig', array(
    			'entities' => $entity,
    			'cantidad' => $cantidad,
    			'empresa' => $empresa,
    			'usuario' => $obj_usuario,
    			'f_i' => $f_inicio,
    			'f_f' => $f_fin
    	));
    
    	$this->get('io_tcpdf')->dir = $obj_sede->getDireccion();
    	$this->get('io_tcpdf')->ciudad = $obj_sede->getCiudad();
    	$this->get('io_tcpdf')->tel = $obj_sede->getTelefono();
    	$this->get('io_tcpdf')->mov = $obj_sede->getMovil();
    	$this->get('io_tcpdf')->mail = $obj_sede->getEmail();
    	$this->get('io_tcpdf')->sede = $obj_sede->getnombre();
    	$this->get('io_tcpdf')->empresa = $obj_sede->getEmpresa()->getNombre();
    
    	$this->get('io_tcpdf')->SetMargins(3, 10, 3);
    
    	return $this->get('io_tcpdf')->quick_pdf($html, 'Honorarios_Medicos.pdf', 'I');
    }
    
    
    public function imprimirCtaCobroAction()
    {
    	$request = $this->get('request');
    	
    	$sede = $request->request->get('sede');
    	$cliente = $request->request->get('cliente');
    	$f_inicio = $request->request->get('f_inicio');
    	$f_fin = $request->request->get('f_fin');

    	$em = $this->getDoctrine()->getEntityManager();
    	
    	if(is_numeric(trim($cliente))){
    		$obj_cliente = $em->getRepository("ParametrizarBundle:Cliente")->find($cliente);
    	}else{
    		$this->get('session')->setFlash('info', 'Debe seleccionar un cliente para continuar..');
    		return $this->redirect($this->generateUrl('factura_cliente_list'));
    	}
    	
    	if(!$obj_cliente){
    		$this->get('session')->setFlash('info', 'El cliente seleccionado no existe.');
    		return $this->redirect($this->generateUrl('factura_cliente_list'));
    	}
    	
    	$obj_sede = null;
    	
    	if(is_numeric(trim($sede))){
    		$obj_sede = $em->getRepository("ParametrizarBundle:Sede")->find($sede);
    		$empresa = $obj_sede->getEmpresa();
    	}else{
    		$sede = 1;
    		$obj_sede = $em->getRepository("ParametrizarBundle:Sede")->find($sede);
    		$empresa = $obj_sede->getEmpresa();
    	}

    	
    	if(is_object($obj_sede)){
    		$con_sede = "AND f.sede =".$sede;
    	}else{
    		$con_sede = "";
    	}
    	
    	$dql= " SELECT
			    	f.id,
			    	p.id as paciente,
			    	p.tipoId,
			    	p.identificacion,
			    	f.fecha,
			    	f.autorizacion,
			    	p.priNombre,
			    	p.segNombre,
			    	p.priApellido,
			    	p.segApellido,
			    	c.cups,
			    	f.valor,
			    	f.copago,
			    	f.descuento,
			    	f.estado
    			FROM
    				ParametrizarBundle:Factura f
    			JOIN
    				f.cargo c
    			JOIN
    				f.paciente p
    			JOIN
    				f.cliente cli
    			WHERE
			    	f.fecha > :inicio AND
			    	f.fecha <= :fin AND			    	
			    	f.estado = :estado AND
			    	f.cliente = :cliente ".
			    	$con_sede."
		    	ORDER BY
		    		f.fecha ASC";
    	 
    	$query = $em->createQuery($dql);
    	
    	$query->setParameter('inicio', $f_inicio.' 00:00:00');
    	$query->setParameter('fin', $f_fin.' 23:59:00');
    	$query->setParameter('cliente', $cliente);
    	$query->setParameter('estado', 'I');
    	
    	$entity = $query->getResult();
    	 
    	$html = $this->renderView('AdminBundle:Reporte:cta_cobro.pdf.twig', array(
    			'entities' => $entity,
    			'empresa' => $empresa,
    			'cliente' => $obj_cliente,
    			'f_i' => $f_inicio,
    			'f_f' => $f_fin
    	));
    	 
    	$this->get('io_tcpdf')->dir = $obj_sede->getDireccion();
    	$this->get('io_tcpdf')->ciudad = $obj_sede->getCiudad();
    	$this->get('io_tcpdf')->tel = $obj_sede->getTelefono();
    	$this->get('io_tcpdf')->mov = $obj_sede->getMovil();
    	$this->get('io_tcpdf')->mail = $obj_sede->getEmail();
    	$this->get('io_tcpdf')->sede = $obj_sede->getnombre();
    	$this->get('io_tcpdf')->empresa = $obj_sede->getEmpresa()->getNombre();
    	 
    	$this->get('io_tcpdf')->SetMargins(3, 10, 3);
    
    	return $this->get('io_tcpdf')->quick_pdf($html, 'Arqueo de Caja Sede '.$obj_sede->getNombre().'.pdf', 'I');
    }
    
	public function buscarRipsAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$sedes = $em->getRepository("ParametrizarBundle:Sede")->findAll();
    	$clientes = $em->getRepository("ParametrizarBundle:Cliente")->findAll();
    	
    	$plantilla = 'AdminBundle:Factura:rips.html.twig';
    	
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("factura_arqueo"));
    	$breadcrumbs->addItem("Factura", $this->get("router")->generate("factura_search"));
    	$breadcrumbs->addItem("Buscar rips");    	

    	return $this->render($plantilla, array(
    			'sedes' => $sedes,
    			'clientes' => $clientes
    	));
    }
    
    
    public function ripsFilesAction()
    {
    	$request = $this->get('request');
    	 
    	$sede = $request->request->get('sede');
    	$cliente = $request->request->get('cliente');
    	$f_inicio = $request->request->get('f_inicio');
    	$f_fin = $request->request->get('f_fin');
    	$factura = $request->request->get('factura');
    	
    	if(trim($f_inicio)){
    		$desde = explode('/',$f_inicio);
    	
    		if(!checkdate($desde[1],$desde[0],$desde[2])){
    			$this->get('session')->setFlash('info', 'La fecha de inicio ingresada es incorrecta.');
    			return $this->redirect($this->generateUrl('factura_rips_search'));
    		}
    		else{
    			$f_inicio = $desde[2].'-'.$desde[1].'-'.$desde[0];
    		}
    	}else{
    		$this->get('session')->setFlash('info', 'La fecha de inicio no puede estar en blanco.');
    		return $this->redirect($this->generateUrl('factura_rips_search'));
    	}
    	 
    	if(trim($f_fin)){
    		$hasta = explode('/',$f_fin);
    	
    		if(!checkdate($hasta[1],$hasta[0],$hasta[2])){
    			$this->get('session')->setFlash('info', 'La fecha de finalización ingresada es incorrecta.');
    			return $this->redirect($this->generateUrl('factura_rips_search'));
    		}else {
    			$f_fin = $hasta[2].'-'.$hasta[1].'-'.$hasta[0];
    		}
    	}else{
    		$this->get('session')->setFlash('info', 'La fecha de finalización no puede estar en blanco.');
    		return $this->redirect($this->generateUrl('factura_rips_search'));
    	}
    
    	$em = $this->getDoctrine()->getEntityManager();
    	 
    	if(is_numeric(trim($cliente))){
    		$cliente = $em->getRepository("ParametrizarBundle:Cliente")->find($cliente);
    	}else{
    		$this->get('session')->setFlash('info', 'Debe seleccionar un cliente para continuar..');
    		return $this->redirect($this->generateUrl('factura_rips_search'));
    	}

    	if(!$cliente){
    		$this->get('session')->setFlash('info', 'El cliente seleccionado no existe.');
    		return $this->redirect($this->generateUrl('factura_rips_search'));
    	}

    	if(is_numeric(trim($sede))){
    		$obj_sede = $em->getRepository("ParametrizarBundle:Sede")->find($sede);
    	}else{
    		$obj_sede = $em->getRepository("ParametrizarBundle:Sede")->find(1);
    		
    		if(!$obj_sede){
    			$this->get('session')->setFlash('info', 'La sede seleccionada no existe.');
    			return $this->redirect($this->generateUrl('factura_rips_search'));
    		}
    	}
    	
    	if(!trim($factura)){
    		$this->get('session')->setFlash('info', 'El No. de factura no puede estar vacio..');
    		return $this->redirect($this->generateUrl('factura_rips_search'));
    	}
    	
    	$dir = $this->container->getParameter('dlaser.directorio.rips');
    	
    	exec("rm -rf ".$dir."*.tar.gz ".$dir."*.txt");

    	$us = $this->fileUS($cliente, $f_inicio, $f_fin);    	
    	$ap = $this->fileAP($cliente, $f_inicio, $f_fin, $factura);
    	$ac = $this->fileAC($cliente, $f_inicio, $f_fin, $factura);
    	$ad = $this->fileAD($cliente, $f_inicio, $f_fin, $factura);
    	$af = $this->fileAF($cliente, $f_inicio, $f_fin, $factura, $obj_sede);
    	
    	$this->fileCt($us, $ap, $ac, $ad, $af);

    	exec("tar -Pzcf ".$dir.$hasta[1].$hasta[2].".tar.gz ".$dir);
    	
		$abririps=$dir.$hasta[1].$hasta[2].".tar.gz";
                    
		$fsize = filesize($abririps);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
        header("Content-Type: application/x-gzip");
        header("Content-Disposition: attachment; filename=\"".basename($abririps)."\";" );
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".$fsize);
                    
        ob_clean();
        flush();
        readfile( $abririps );
    }

    private function fileUS($cliente, $f_inicio, $f_fin){
    	
    	$dir = $this->container->getParameter('dlaser.directorio.rips');
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$dql= " SELECT
    				DISTINCT
    					p.identificacion AS id,
			    	p.tipoId,			    	
			    	p.priApellido,
			    	p.segApellido,
			    	p.priNombre,
			    	p.segNombre,
			    	p.fN,
			    	p.sexo,
			    	p.depto,
			    	p.mupio,
			    	p.zona,
			    	cli.codEps
		    	FROM
		    		ParametrizarBundle:Factura f
		    	JOIN
		    	   	f.paciente p
		    	JOIN
		    		f.cliente cli
		    	WHERE
			    	f.fecha > :inicio AND
			    	f.fecha <= :fin AND
			    	f.estado = :estado AND
			    	f.cliente = :cliente 
		    	ORDER BY
		    		f.fecha ASC";
    	
    	$query = $em->createQuery($dql);
    	 
    	$query->setParameter('inicio', $f_inicio.' 00:00:00');
    	$query->setParameter('fin', $f_fin.' 23:59:00');
    	$query->setParameter('cliente', $cliente->getId());
    	$query->setParameter('estado', 'I');
    	 
    	$entity = $query->getArrayResult();
    	
    	$gestor = fopen($dir."US.txt", "w+");
    	
    	if (!$gestor){
    		$this->get('session')->setFlash('info', 'No se puede crear txt.');
    		return $this->redirect($this->generateUrl('factura_rips_search'));
    	}    	
    	
    	$date2 = new \DateTime("now");
    	
    	foreach ($entity as $value){
    		$fn = new \DateTime($value['fN']);
    		$interval = $fn->diff($date2);
    		fwrite($gestor, "".$value['tipoId'].",".$value['id'].",".$value['codEps'].",1,".$value['priApellido'].",".$value['segApellido'].",".$value['priNombre'].",".$value['segNombre'].",".$interval->format('%y').",1,".$value['sexo'].",".$value['depto'].",".$value['mupio'].",".$value['zona']."\r\n");
    	} 	
    	
    	return count($entity);
    }
    
    private function fileAP($cliente, $f_inicio, $f_fin, $factura){
    	 
    	$dir = $this->container->getParameter('dlaser.directorio.rips');
    	 
    	$em = $this->getDoctrine()->getEntityManager();
    	 
    	$dql= " SELECT    	
			    	p.identificacion AS id,
			    	p.tipoId,
			    	f.fecha,
			    	f.autorizacion,
			    	c.cups,
			    	f.valor
		    	FROM
		    		ParametrizarBundle:Factura f
		    	JOIN
		    		f.paciente p
		    	JOIN
		    		f.cargo c
		    	WHERE
			    	f.fecha > :inicio AND
			    	f.fecha <= :fin AND
			    	f.estado = :estado AND
			    	f.cliente = :cliente AND
			    	c.cups != '890202'
		    	ORDER BY
		    		f.fecha ASC";
    	 
    	$query = $em->createQuery($dql);
    
    	$query->setParameter('inicio', $f_inicio.' 00:00:00');
    	$query->setParameter('fin', $f_fin.' 23:59:00');
    	$query->setParameter('cliente', $cliente->getId());
    	$query->setParameter('estado', 'I');
    
    	$entity = $query->getArrayResult();
    	 
    	$gestor = fopen($dir."AP.txt", "w+");
    	 
    	if (!$gestor){
    		$this->get('session')->setFlash('info', 'No se puede crear txt.');
    		return $this->redirect($this->generateUrl('factura_rips_search'));
    	}
    	 
    	foreach ($entity as $value){
    		
    		$fecha = new \DateTime($value['fecha']);
    		
    		fwrite($gestor, "".$factura.",761110730901,".$value['tipoId'].",".$value['id'].",".$fecha->format('d/m/Y').",".$value['autorizacion'].",".$value['cups'].",1,1,1,,,,,".$value['valor']."\r\n");
    	}
    	 
    	return count($entity);
    }
    
    private function fileAC($cliente, $f_inicio, $f_fin, $factura){
    
    	$dir = $this->container->getParameter('dlaser.directorio.rips');
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$dql= " SELECT
			    	p.identificacion AS id,
			    	p.tipoId,
			    	f.fecha,
			    	f.autorizacion,
			    	c.cups,
			    	f.valor,
			    	f.copago
		    	FROM
		    		ParametrizarBundle:Factura f
		    	JOIN
		    		f.paciente p
		    	JOIN
		    		f.cargo c
		    	WHERE
			    	f.fecha > :inicio AND
			    	f.fecha <= :fin AND
			    	f.estado = :estado AND
			    	f.cliente = :cliente AND
			    	c.cups = '890202'
		    	ORDER BY
		    		f.fecha ASC";
    
    	$query = $em->createQuery($dql);
    
    	$query->setParameter('inicio', $f_inicio.' 00:00:00');
    	$query->setParameter('fin', $f_fin.' 23:59:00');
    	$query->setParameter('cliente', $cliente->getId());
    	$query->setParameter('estado', 'I');
    
    	$entity = $query->getArrayResult();
    
    	$gestor = fopen($dir."AC.txt", "w+");
    
    	if (!$gestor){
    		$this->get('session')->setFlash('info', 'No se puede crear txt.');
    		return $this->redirect($this->generateUrl('factura_rips_search'));
    	}
    
    	foreach ($entity as $value){
    
    		$fecha = new \DateTime($value['fecha']);
    
    		fwrite($gestor, "".$factura.",761110730901,".$value['tipoId'].",".$value['id'].",".$fecha->format('d/m/Y').",".$value['autorizacion'].",".$value['cups'].",10,15,,,,,1,".$value['valor'].".00,".$value['copago'].".00,".($value['valor']-$value['copago']).".00\r\n");
    	}
    
    	return count($entity);
    }
    
    private function fileAD($cliente, $f_inicio, $f_fin, $factura){
    
    	$dir = $this->container->getParameter('dlaser.directorio.rips');
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$dql= " SELECT
			    	f.id,
			    	c.cups,
			    	f.valor,
			    	f.copago
		    	FROM
		    		ParametrizarBundle:Factura f
		    	JOIN
		    		f.paciente p
		    	JOIN
		    		f.cargo c
		    	WHERE
			    	f.fecha > :inicio AND
			    	f.fecha <= :fin AND
			    	f.estado = :estado AND
			    	f.cliente = :cliente AND
			    	c.cups = '890202'
    	";
    
    	$query = $em->createQuery($dql);
    
    	$query->setParameter('inicio', $f_inicio.' 00:00:00');
    	$query->setParameter('fin', $f_fin.' 23:59:00');
    	$query->setParameter('cliente', $cliente->getId());
    	$query->setParameter('estado', 'I');
    
    	$entity = $query->getArrayResult();
    	
    	$dql= " SELECT
			    	c.cups,
			    	f.valor,
			    	f.copago
		    	FROM
		    		ParametrizarBundle:Factura f
		    	JOIN
		    		f.paciente p
		    	JOIN
		    		f.cargo c
		    	WHERE
			    	f.fecha > :inicio AND
			    	f.fecha <= :fin AND
			    	f.estado = :estado AND
			    	f.cliente = :cliente AND
			    	c.cups != '890202'
    	";
    	
    	$query = $em->createQuery($dql);
    	
    	$query->setParameter('inicio', $f_inicio.' 00:00:00');
    	$query->setParameter('fin', $f_fin.' 23:59:00');
    	$query->setParameter('cliente', $cliente->getId());
    	$query->setParameter('estado', 'I');
    	
    	$entity2 = $query->getArrayResult();

    	$gestor = fopen($dir."AD.txt", "w+");
    
    	if (!$gestor){
    		$this->get('session')->setFlash('info', 'No se puede crear txt.');
    		return $this->redirect($this->generateUrl('factura_rips_search'));
    	}
    	
		$num_consulta = 0;
		$val_consulta = 0;
		$copago_consulta = 0; 
    	foreach ($entity as $value){
    		$val_consulta+=$value['valor'];
    		$copago_consulta+=$value['copago'];
    		$num_consulta++;
    	}
    	
    	fwrite($gestor, "".$factura.",761110730901,01,".$num_consulta.",,".($val_consulta-$copago_consulta).".00\r\n");
    	
    	$num_px = 0;
    	$val_px = 0;
    	$copago_px = 0;
    	foreach ($entity2 as $value){
    		$val_px+=$value['valor'];
    		$copago_px+=$value['copago'];
    		$num_px++;
    	}	
    	 
    	fwrite($gestor, "".$factura.",761110730901,02,".$num_px.",,".($val_px-$copago_px).".00\r\n");
    
    	return 2;
    }
    
    private function fileAF($cliente, $f_inicio, $f_fin, $factura, $obj_sede){
    
    	$dir = $this->container->getParameter('dlaser.directorio.rips');
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$dql= " SELECT
			    	SUM (f.valor) AS valor,
			    	SUM (f.copago) AS copago
		    	FROM
		    		ParametrizarBundle:Factura f
		    	WHERE
			    	f.fecha > :inicio AND
			    	f.fecha <= :fin AND
			    	f.estado = :estado AND
			    	f.cliente = :cliente
		";
    
    	$query = $em->createQuery($dql);
    
    	$query->setParameter('inicio', $f_inicio.' 00:00:00');
    	$query->setParameter('fin', $f_fin.' 23:59:00');
    	$query->setParameter('cliente', $cliente->getId());
    	$query->setParameter('estado', 'I');
    
    	$entity = $query->getArrayResult();
    
    	$gestor = fopen($dir."AF.txt", "w+");
    
    	if (!$gestor){
    		$this->get('session')->setFlash('info', 'No se puede crear txt.');
    		return $this->redirect($this->generateUrl('factura_rips_search'));
    	}
    	
    	$fecha = new \DateTime('now');
    	$inicio = new \DateTime($f_inicio);
    	$fin = new \DateTime($f_fin);
    	
    	$contrato = $em->getRepository("ParametrizarBundle:Contrato")->findOneBy(array('cliente' => $cliente->getId(), 'sede' => $obj_sede->getId()));
    
    	fwrite($gestor, "761110730901,CENTRO CARDIOLOGICO DEL VALLE,NI,900260604,".$factura.",".$fecha->format('d/m/Y').",".$inicio->format('d/m/Y').",".$fin->format('d/m/Y').",".$cliente->getCodEps().",".$cliente->getRazon().",,ISS 2001 + ".$contrato->getPorcentaje()."%,,".$entity[0]['copago'].".00,0.00,0.00,".($entity[0]['valor']-$entity[0]['copago']).".00\r\n");
    
    	return count($entity);
    }
    
    private function fileCt($us, $ap, $ac, $ad, $af){
    	 
    	$dir = $this->container->getParameter('dlaser.directorio.rips');
    	 
    	$gestor = fopen($dir."CT.txt", "w+");
    	 
    	if (!$gestor){
    		$this->get('session')->setFlash('info', 'No se puede crear txt.');
    		return $this->redirect($this->generateUrl('factura_rips_search'));
    	}
			
    	$fecha = new \DateTime('now');
    	
    	fwrite($gestor, "761110730901,".$fecha->format('d/m/Y').",US,".$us."\r\n");
    	fwrite($gestor, "761110730901,".$fecha->format('d/m/Y').",AF,".$af."\r\n");
    	fwrite($gestor, "761110730901,".$fecha->format('d/m/Y').",AD,".$ad."\r\n");
    	fwrite($gestor, "761110730901,".$fecha->format('d/m/Y').",AC,".$ac."\r\n");
    	fwrite($gestor, "761110730901,".$fecha->format('d/m/Y').",AP,".$ap."\r\n");
    	 
    	return true;
    }
    
    public function facturacionPreviaAction()
    {
    	$request = $this->get('request');
    
    	$sede = $request->request->get('sede');
    	$cliente = $request->request->get('cliente');
    	$f_inicio = $request->request->get('f_inicio');
    	$f_fin = $request->request->get('f_fin');
    	 
    	$em = $this->getDoctrine()->getEntityManager();
    
    	if(is_numeric(trim($cliente))){
    		$obj_cliente = $em->getRepository("ParametrizarBundle:Cliente")->find($cliente);
    	}else{
    		$this->get('session')->setFlash('info', 'Debe ingresar un cliente valido..');
    		return $this->redirect($this->generateUrl('factura_cliente_list'));
    	}
    
    	if(!$obj_cliente){
    		$this->get('session')->setFlash('info', 'El cliente seleccionado no existe.');
    		return $this->redirect($this->generateUrl('factura_cliente_list'));
    	}
    
    	$obj_sede = null;
    	 
    	if(is_numeric(trim($sede))){
    		$obj_sede = $em->getRepository("ParametrizarBundle:Sede")->find($sede);
    		$con_sede = "AND f.sede =".$sede;
    		$empresa = $obj_sede->getEmpresa();
    	}else{
    		$obj_sede = $em->getRepository("ParametrizarBundle:Sede")->find('1');
    		$empresa = $obj_sede->getEmpresa();
    		$con_sede = "";
    	}
    
    	$dql= " SELECT
    				SUM(f.valor) AS valor,
    				SUM(f.copago) AS copago
    			FROM
    				ParametrizarBundle:Factura f
    			JOIN
    				f.cargo c
    			JOIN
    				f.paciente p
    			JOIN
    				f.cliente cli
    			WHERE
			    	f.fecha > :inicio AND
			    	f.fecha <= :fin AND
    				f.estado = :estado AND
			    	f.cliente = :cliente ".
			    	$con_sede;
    	 
    	$query = $em->createQuery($dql);
    
    	$query->setParameter('inicio', $f_inicio.' 00:00:00');
    	$query->setParameter('fin', $f_fin.' 23:59:00');
    	$query->setParameter('cliente', $cliente);
    	$query->setParameter('estado', 'I');
    
    	$valor = $query->getSingleResult();
    	
    	if($con_sede=="") $sedes = 0;
    	else $sedes = $obj_sede->getId();
    	
    	$entity = new Facturacion();
    	
    	$entity->setInicio($f_inicio);
    	$entity->setFin($f_fin);
    	$entity->setSedes($sedes);
    	$entity->setConcepto("Por servicios integrales de cardiologia en la toma e interpretación de imagenes diagnosticas en le mes de");
    	$entity->setSubtotal($valor['valor'] - $valor['copago']);
    	$entity->setIva(0);
    	
    	$form   = $this->createForm(new FacturacionType(), $entity);
    	
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
    	$breadcrumbs->addItem("Factura", $this->get("router")->generate("factura_search"));
    	$breadcrumbs->addItem("Final nuevo");
    	
    	return $this->render('AdminBundle:Factura:factura_previa.html.twig', array(
    			'valores' => $valor,
    			'cliente' => $obj_cliente,
    			'sede' => $obj_sede,
    			'f_i' => $f_inicio,
    			'f_f' => $f_fin,
    			'form'   => $form->createView()
    	));
    }
    
    
    public function facturacionSaveAction($cliente, $sede)
    {
    	$entity  = new Facturacion();
    
    	$request = $this->getRequest();
    	$form    = $this->createForm(new FacturacionType(), $entity);
    	$form->bindRequest($request);
    
    	if ($form->isValid()) {
    		
    		$registro = $form->getData();
    
    		$em = $this->getDoctrine()->getEntityManager();
    		    
    		$cliente = $em->getRepository('ParametrizarBundle:Cliente')->find($cliente);
    		$sede = $em->getRepository('ParametrizarBundle:Sede')->find($sede);
    		
    		$inicio = new \DateTime($registro->getInicio());
    		$fin = new \DateTime($registro->getFin());
   
    		$entity->setFecha(new \DateTime('now'));
    		$entity->setInicio($inicio);
    		$entity->setFin($fin);
    		$entity->setEstado('G');
    		$entity->setCliente($cliente);
    		$entity->setSede($sede);
    		   
    		$em->persist($entity);
    		$em->flush();
    
    		$this->get('session')->setFlash('info', 'La información de la factura ha sido registrada éxitosamente.');
    
    		return $this->redirect($this->generateUrl('factura_final_show',array("id"=>$entity->getId())));
    
    	}
    
    	return $this->render('AdminBundle:Factura:factura_previa.html.twig', array(
    			'valores' => $valor,
    			'cliente' => $obj_cliente,
    			'sede' => $obj_sede,
    			'f_i' => $f_inicio,
    			'f_f' => $f_fin,
    			'form'   => $form->createView()
    	));
    
    }
    
    
    public function facturacionShowAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$factura = $em->getRepository('ParametrizarBundle:Facturacion')->find($id);
    
    
    	if (!$factura) {
    		throw $this->createNotFoundException('La factura solicitada no esta disponible.');
    	}
    	
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("factura_arqueo"));
    	$breadcrumbs->addItem("Factura", $this->get("router")->generate("factura_search"));
    	$breadcrumbs->addItem("Factura venta");
    
    	return $this->render('AdminBundle:Factura:factura_final_show.html.twig', array(
    			'entity'  => $factura    
    	));
    }
    
    public function facturacionImprimirAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entity = $em->getRepository('ParametrizarBundle:Facturacion')->find($id);
    	 
    	if (!$entity) {
    		throw $this->createNotFoundException('La factura a imprimir no esta disponible.');
    	}

    	$cliente = $entity->getCliente();
    	$sede = $entity->getSede();
    
    	$html = $this->renderView('AdminBundle:Factura:factura_venta.pdf.twig',
    			array('entity' 	=> $entity,
    					'cliente'	=> $cliente,
    					'sede'=>$sede
    			));
    	 
    	 
    	$this->get('io_tcpdf')->dir = $sede->getDireccion();
    	$this->get('io_tcpdf')->ciudad = $sede->getCiudad();
    	$this->get('io_tcpdf')->tel = $sede->getTelefono();
    	$this->get('io_tcpdf')->mov = $sede->getMovil();
    	$this->get('io_tcpdf')->mail = $sede->getEmail();
    	$this->get('io_tcpdf')->sede = $sede->getnombre();
    	$this->get('io_tcpdf')->empresa = $sede->getEmpresa()->getNombre();
    
    	return $this->get('io_tcpdf')->quick_pdf($html, 'factura.pdf', 'I');
    }
    
    
    public function facturacionRipsAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$entity = $em->getRepository("ParametrizarBundle:Facturacion")->find($id);
    	
    	$f_inicio = $entity->getInicio()->format("Y-m-d");
    	$f_fin = $entity->getFin()->format("Y-m-d");
    	
    	$cliente = $entity->getCliente();
    	$factura = "CC".$entity->getId();
    	$obj_sede = $entity->getSede();
    	 
    	$dir = $this->container->getParameter('dlaser.directorio.rips');
    	 
    	exec("rm -rf ".$dir."*.tar.gz ".$dir."*.txt");
    
    	$us = $this->fileUS($cliente, $f_inicio, $f_fin);
    	$ap = $this->fileAP($cliente, $f_inicio, $f_fin, $factura);
    	$ac = $this->fileAC($cliente, $f_inicio, $f_fin, $factura);
    	$ad = $this->fileAD($cliente, $f_inicio, $f_fin, $factura);
    	$af = $this->fileAF($cliente, $f_inicio, $f_fin, $factura, $obj_sede);
    	 
    	$this->fileCt($us, $ap, $ac, $ad, $af);
    
    	exec("tar -Pzcf ".$dir.$entity->getFin()->format("m_d").".tar.gz ".$dir);
    	 
    	$abririps=$dir.$entity->getFin()->format("m_d").".tar.gz";
    
    	$fsize = filesize($abririps);
    
    	header("Pragma: public");
    	header("Expires: 0");
    	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    	header("Cache-Control: private",false);
    	header("Content-Type: application/x-gzip");
    	header("Content-Disposition: attachment; filename=\"".basename($abririps)."\";" );
    	header("Content-Transfer-Encoding: binary");
    	header("Content-Length: ".$fsize);
    
    	ob_clean();
    	flush();
    	readfile( $abririps );
    }
    
}