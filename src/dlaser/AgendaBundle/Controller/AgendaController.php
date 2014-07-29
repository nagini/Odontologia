<?php

namespace dlaser\AgendaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use dlaser\AgendaBundle\Entity\Restriccion;
use dlaser\AgendaBundle\Entity\Agenda;
use dlaser\AgendaBundle\Entity\Cupo;
use dlaser\AgendaBundle\Form\RestriccionType;
use dlaser\AgendaBundle\Form\AgendaType;
use dlaser\AgendaBundle\Form\AgendaMedicaType;


class AgendaController extends Controller
{
    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();    
        $agenda = $em->getRepository('AgendaBundle:Agenda')->findAll();
        
        $query = $em->createQuery(' SELECT a
                FROM AgendaBundle:Agenda a
                WHERE a.fechaFin > :fi
                ORDER BY a.fechaInicio ASC');        
        
        $query->setParameter('fi', new \DateTime('now'));        
        $agenda = $query->getResult();
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Listar");
    
        return $this->render('AgendaBundle:Agenda:list.html.twig', array(
                'agendas'  => $agenda
        ));        
    }

    public function newAction()
    {
        $entity = new Agenda();
        $entity->setFechaInicio(new \DateTime('now'));
        $entity->setFechaFin(new \DateTime('now'));
        
        $form   = $this->createForm(new AgendaType(), $entity);
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Nuevo");
                   
        return $this->render('AgendaBundle:Agenda:new.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView()
        ));
    }


    public function saveAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
                
        $entity  = new Agenda();
        $request = $this->getRequest();
        $form    = $this->createForm(new AgendaType(), $entity);
        $form->bindRequest($request);
    
        if ($form->isValid()) {
                       
            $em->persist($entity);
            $em->flush();
                       
            /*$ncupos = ((($entity->getFechaFin()->getTimestamp() - $entity->getFechaInicio()->getTimestamp()) / 60) / $entity->getIntervalo());
            
            $turno = $entity->getFechaInicio();
            
            for($i = 0; $i < $ncupos; $i++ ){

                $cupo = new Cupo();                
                $cupo->setHora($turno);
                $cupo->setAgenda($entity);
                $cupo->setEstado('L');
                $em->persist($cupo);
                $em->flush();
                $turno->add(new \DateInterval('PT'.$entity->getIntervalo().'M'));                
            }*/
    
            $this->get('session')->setFlash('ok', 'La agenda ha sido creada éxitosamente.');    
            return $this->redirect($this->generateUrl('agenda_show', array("id" => $entity->getId())));    
        }
    
        return $this->render('AgendaBundle:Agenda:new.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView()
        ));
    }
    
    
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();    
        $agenda = $em->getRepository('AgendaBundle:Agenda')->find($id);
    
        if (!$agenda) {
            throw $this->createNotFoundException('La agenda solicitada no existe.');
        }        
        $firewalls = $em->getRepository('AgendaBundle:Restriccion')->findByAgenda($id);
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_list"));        
        $breadcrumbs->addItem("Detalle");
    
        return $this->render('AgendaBundle:Agenda:show.html.twig', array(
                'agenda'  => $agenda,
                'restricciones' => $firewalls
        ));
    }
    
    
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();    
        $entity = $em->getRepository('AgendaBundle:Agenda')->find($id);
    
        if (!$entity) {
            throw $this->createNotFoundException('La agenda solicitada no existe');
        }
    
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_list"));                
        $breadcrumbs->addItem("Detalle",$this->get("router")->generate("agenda_show",array("id" => $id)));
        $breadcrumbs->addItem("Modificar");
        
        $editForm = $this->createForm(new AgendaType(), $entity);
    
        return $this->render('AgendaBundle:Agenda:edit.html.twig', array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
        ));
    }
    
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
    
        $entity = $em->getRepository('AgendaBundle:Agenda')->find($id);
    
        if (!$entity) {
            throw $this->createNotFoundException('La agenda solicitada no existe.');
        }
    
        $editForm   = $this->createForm(new AgendaType(), $entity);    
        $request = $this->getRequest();    
        $editForm->bindRequest($request);
    
        if ($editForm->isValid()) {
    
            $em->persist($entity);
            $em->flush();
    
            $this->get('session')->setFlash('ok', 'La agenda ha sido modificada éxitosamente.');    
            return $this->redirect($this->generateUrl('agenda_edit', array('id' => $id)));
        }
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_list"));                
        $breadcrumbs->addItem("Detalle",$this->get("router")->generate("agenda_show",array("id" => $id)));
        $breadcrumbs->addItem("Modificar");
    
        return $this->render('AgendaBundle:Agenda:edit.html.twig', array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView()
        ));
    }
    
    
    
    public function ajaxBuscarAction(){
    
        $request = $this->get('request');
        $cliente=$request->request->get('cliente');
        $sede=$request->request->get('sede');
        $cargo=$request->request->get('cargo');
    
        if(is_numeric($cliente) && is_numeric($sede) && is_numeric($cargo)){
    
            $em = $this->getDoctrine()->getEntityManager();
            
            $query = $em->createQuery(' SELECT 
            								a 
                                        FROM 
            								AgendaBundle:Agenda a
                                        WHERE 
            								a.fechaFin > :fecha AND
                                        	a.sede = :sede
                                        ORDER BY 
            								a.fechaInicio ASC');
            
            
            $query->setParameter('fecha', new \DateTime('now'));
            $query->setParameter('sede', $sede);
            
            $agendas = $query->getResult();

            if($agendas){
    
                $response=array("responseCode"=>200);
    
                foreach($agendas as $value)
                {
                    $response['agenda'][$value->getId()] = $value->getFechaInicio()->format('d-m-Y H:i')." - ".$value->getFechaFin()->format('H:i')." - ".$value->getNota();             
                }
            }else{
                    $response=array("responseCode"=>400, "msg"=>"No hay agendas disponibles para la actividad seleccionada en la sede");
            }
        
        $return=json_encode($response);
        return new Response($return,200,array('Content-Type'=>'application/json'));
        
       }
    }
    
    public function ajaxListaAction(){
    
        $request = $this->get('request');        
        $sede=$request->request->get('sede');
            
        if(is_numeric($sede)){
        	
        	$fecha = new \DateTime('now');
    
            $em = $this->getDoctrine()->getEntityManager();
    
            $query = $em->createQuery(' SELECT a
                    FROM AgendaBundle:Agenda a
                    WHERE a.fechaInicio > :fi
                    AND a.sede = :sede
                    ORDER BY a.fechaInicio ASC');
    
            $query->setParameter('fi', $fecha->format('Y-m-d 00:00:00'));
            $query->setParameter('sede', $sede);
    
            $agendas = $query->getArrayResult();
    
            if($agendas){
    
                $response=array("responseCode"=>200);
    
                foreach($agendas as $key => $value)
                {
                    $response['agenda'][$key] = $value;
                }
            }else{
                $response=array("responseCode"=>400, "msg"=>"No hay agendas disponibles para la sede seleccionada");
            }
    
            $return=json_encode($response);
            return new Response($return,200,array('Content-Type'=>'application/json'));
    
        }
    }
    
    public function agendaMedicaAction(){
        
        $user = $this->get('security.context')->getToken()->getUser();
        $id=$user->getId();
                
        $form   = $this->createForm(new AgendaMedicaType(array('user' => $id)));
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_medica_list"));
        $breadcrumbs->addItem("Medica");
        
        return $this->render('AgendaBundle:Agenda:agenda_medica.html.twig', array(
                'form' => $form->createView(),
        ));
    }

    public function agendaAuxiliarAction(){
    
    	$user = $this->get('security.context')->getToken()->getUser();
    	$id=$user->getId();
    
    	$form   = $this->createForm(new AgendaMedicaType(array('user' => $id)));
    
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
    	$breadcrumbs->addItem("Agenda actividades", $this->get("router")->generate("agenda_aux_list"));
    	$breadcrumbs->addItem("Preinformar");
    	
    	return $this->render('AgendaBundle:Agenda:agenda_auxiliar.html.twig', array(
    			'form' => $form->createView(),
    	));
    }

    public function ajaxAgendaMedicaBuscarAction(){
    
        $request = $this->get('request');
        $sede=$request->request->get('sede');
    
        if(is_numeric($sede)){
    
            $em = $this->getDoctrine()->getEntityManager();
            
            $user = $this->get('security.context')->getToken()->getUser();
            $usuario = $user->getId();
    
            $query = $em->createQuery(" SELECT f.id,
                    c.hora,
                    f.fecha,
            		f.id,
                    p.identificacion,
                    p.priNombre,
                    p.segNombre,
                    p.priApellido,
                    p.segApellido,
                    cli.nombre as cliente,
                    car.nombre as cargo,
                    car.cups,
                    f.observacion,
            		f.estado
                    FROM ParametrizarBundle:Factura f
                    LEFT JOIN f.cupo c
                    LEFT JOIN f.cliente cli
                    LEFT JOIN f.paciente p
                    LEFT JOIN f.cargo car
                    LEFT JOIN c.agenda a
                    WHERE f.sede = :sede
                    AND f.estado != :estado
            		AND f.estado != 'X'
            		AND car.cups NOT IN (933600)
                    AND a.usuario = :usuario
                    ORDER BY c.hora ASC");

            $fecha = new \DateTime('now');

            $query->setParameter('sede', $sede);
            $query->setParameter('estado', 'I');
            $query->setParameter('usuario', $usuario);

            $agendas = $query->getArrayResult();

            if($agendas){

                $response=array("responseCode"=>200);

                foreach($agendas as $key => $value)
                {
                    $response['agenda'][$key] = $value;
                }
            }else{
                $response=array("responseCode"=>400, "msg"=>"No hay actividades pendientes para la sede seleccionada");
            }

            $return=json_encode($response);
            return new Response($return,200,array('Content-Type'=>'application/json'));
    
        }
    }
    
    public function ajaxAgendaAuxiliarBuscarAction(){
    
    	$request = $this->get('request');
    	$sede=$request->request->get('sede');
    
    	if(is_numeric($sede)){
    
    		$em = $this->getDoctrine()->getEntityManager();    
    
    		$query = $em->createQuery(' SELECT f.id,
    				c.hora,
    				f.fecha,
    				p.identificacion,
    				p.priNombre,
    				p.segNombre,
    				p.priApellido,
    				p.segApellido,
    				cli.nombre as cliente,
    				car.nombre as cargo,
    				car.cups,
    				f.observacion,
    				f.estado
    				FROM ParametrizarBundle:Factura f
    				LEFT JOIN f.cupo c
    				LEFT JOIN f.cliente cli
    				LEFT JOIN f.paciente p
    				LEFT JOIN f.cargo car
    				WHERE f.sede = :sede
    				AND f.estado = :estado
    				AND car.cups IN (896100, 894102, 895001, 920407, 920408, 881234, 881236, 890202)
    				ORDER BY c.hora ASC');
    		
    
    		$fecha = new \DateTime('now');
    
    		$query->setParameter('sede', $sede);
    		$query->setParameter('estado', 'P');
    
    		$agendas = $query->getArrayResult();
    
    		if($agendas){
    
    			$response=array("responseCode"=>200);
    
    			foreach($agendas as $key => $value)
    			{
    				$response['agenda'][$key] = $value;
    			}
    		}else{
    			$response=array("responseCode"=>400, "msg"=>"No hay actividades pendientes para la sede seleccionada");
    		}
    
    		$return=json_encode($response);
    		return new Response($return,200,array('Content-Type'=>'application/json'));
    
    	}
    }
}