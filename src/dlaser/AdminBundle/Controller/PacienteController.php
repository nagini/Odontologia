<?php

namespace dlaser\AdminBundle\Controller;

use dlaser\AdminBundle\Form\AfiliacionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use dlaser\ParametrizarBundle\Entity\Paciente;
use dlaser\ParametrizarBundle\Entity\Afiliacion;
use dlaser\AdminBundle\Form\PacienteType;
use dlaser\AdminBundle\Form\pacienteSearchType;
use Symfony\Component\HttpFoundation\Response;

class PacienteController extends Controller
{

    public function listAction($char)
    {    	   	
        $em = $this->getDoctrine()->getEntityManager();
    	$paginador = $this->get('ideup.simple_paginator');
    	$paginador->setItemsPerPage(15);
    	$form   = $this->createForm(new pacienteSearchType());
    	
    	$dql = $em->createQuery("SELECT p FROM ParametrizarBundle:Paciente p
						WHERE p.priNombre LIKE :nombre ORDER BY p.priNombre, p.priApellido ASC");
    	
    	$dql->setParameter('nombre', $char.'%');
    	$pacientes = $paginador->paginate($respusta = $dql->getResult())->getResult();
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Paciente",$this->get("router")->generate("paciente_list",array("char" => $char)));
        $breadcrumbs->addItem("Listar");
        
                
        return $this->render('AdminBundle:Paciente:list.html.twig', array(
    			'entities'  => $pacientes,    			
    			'char' => $char,
    			'form'   => $form->createView()
    	));
    }
    public function pacisearchAction()
    {
    	$request = $this->getRequest();
    	$form   = $this->createForm(new pacienteSearchType());
    	$form->bindRequest($request);
    	 
    	if($form->isValid())
    	{
    		// de acuerdo a la opcion ingresada por el usuario se establece la consulta a la base de datos
    		 
    		$nombre = $form->get('nombre')->getData();
    		$option = $form->get('option')->getData();
    
    		 
    		$em = $this->getDoctrine()->getEntityManager();
    		$paginador = $this->get('ideup.simple_paginator');
    		$paginador->setItemsPerPage(15);
    		$form   = $this->createForm(new pacienteSearchType());
    		 
    		if($option == 'nombre')
    		{
    			$dql = $em->createQuery("SELECT p FROM ParametrizarBundle:Paciente p
						WHERE p.priNombre LIKE :nombre ORDER BY p.priNombre, p.priApellido ASC");
    			 
    			$dql->setParameter('nombre', $nombre.'%');
    			$pacientes = $paginador->paginate($respusta = $dql->getResult())->getResult();
    
    		}
    		elseif($option == 'apellido')
    		{
    			$dql = $em->createQuery("SELECT p FROM ParametrizarBundle:Paciente p
						WHERE p.priApellido LIKE :apellido ORDER BY p.priNombre, p.priApellido ASC");
    			 
    			$dql->setParameter('apellido', $nombre.'%');
    			$pacientes = $paginador->paginate($respusta = $dql->getResult())->getResult();
    		}
    		else if($option == 'cedula' && is_numeric($nombre))
    		{
    			$dql = $em->createQuery("SELECT p FROM ParametrizarBundle:Paciente p
						WHERE p.identificacion LIKE :cedula ORDER BY p.priNombre, p.priApellido ASC");
    			 
    			$dql->setParameter('cedula', $nombre.'%');
    			$pacientes = $paginador->paginate($respusta = $dql->getResult())->getResult();
    		}
    		 
    	}else{    		
    		return $this->redirect($this->generateUrl('paciente_list', array("char" => 'A')));
    	}
    	
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
    	$breadcrumbs->addItem("Paciente",$this->get("router")->generate("paciente_list",array("char" => $nombre)));
    	$breadcrumbs->addItem("Listar");
    
    	return $this->render('AdminBundle:Paciente:list.html.twig', array(
    			'entities'  => $pacientes,
    			'char' => $nombre,
    			'form'   => $form->createView()
    	));
    }
    
    public function newAction()
    {
        $entity = new Paciente();
        $form   = $this->createForm(new PacienteType(), $entity);        
        $user = $this->get('security.context')->getToken()->getUser();
                
        if ($user->getPerfil() == 'ROLE_AUX') {
        	$plantilla = 'ParametrizarBundle:Paciente:new.html.twig';
        }
        elseif ($user->getPerfil() == 'ROLE_ADMIN') {
        	$plantilla = 'AdminBundle:Paciente:new.html.twig';
        }        
    
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Paciente",$this->get("router")->generate("paciente_list",array("char" => 'A')));
        $breadcrumbs->addItem("Nuevo");
        
        return $this->render($plantilla, array(        		
                'entity' => $entity,
                'form'   => $form->createView()
        ));
    }
    
    public function saveAction()
    {
        $entity  = new Paciente();
        $request = $this->getRequest();
        $form    = $this->createForm(new PacienteType(), $entity);
        $form->bindRequest($request);
    
        if ($form->isValid()) {
             
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();
    
            $this->get('session')->setFlash('ok', 'El paciente ha sido creado éxitosamente.');      
            return $this->redirect($this->generateUrl('paciente_show', array("id" => $entity->getId())));
    
        }        
    	$user = $this->get('security.context')->getToken()->getUser();
                
        if ($user->getPerfil() == 'ROLE_AUX') {
        	$plantilla = 'ParametrizarBundle:Paciente:new.html.twig';
        }
        elseif ($user->getPerfil() == 'ROLE_ADMIN') {
        	$plantilla = 'AdminBundle:Paciente:new.html.twig';
        } 
    
        return $this->render($plantilla, array(
                'entity' => $entity,
                'form'   => $form->createView()
        ));
    
    }
    
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();    
        $paciente = $em->getRepository('ParametrizarBundle:Paciente')->find($id);
    
        if (!$paciente) {
            throw $this->createNotFoundException('El paciente solicitado no existe.');
        }
        
        $afiliaciones = $em->getRepository('ParametrizarBundle:Afiliacion')->findByPaciente($id);        
        $afiliacion = new Afiliacion();        
        $form = $this->createForm(new AfiliacionType(), $afiliacion);
        
        $vars = array('paciente' => $paciente,
                      'afiliaciones' => $afiliaciones,
                      'form' => $form->createView());
        
   		$user = $this->get('security.context')->getToken()->getUser();
                
        if ($user->getPerfil() == 'ROLE_AUX') {
        	$plantilla = 'ParametrizarBundle:Paciente:show.html.twig';
        }
        elseif ($user->getPerfil() == 'ROLE_ADMIN') {
        	$plantilla = 'AdminBundle:Paciente:show.html.twig';
        } 
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Paciente",$this->get("router")->generate("paciente_search"));        
        $breadcrumbs->addItem("Detalle ".$paciente->getPriNombre());
    	
        return $this->render($plantilla, $vars);
    }
    
    public function editAction($id)
    {    	
    	$em = $this->getDoctrine()->getEntityManager();    
        $entity = $em->getRepository('ParametrizarBundle:Paciente')->find($id);        
    
        if (!$entity) {
            throw $this->createNotFoundException('El paciente solicitado no existe');
        }
    
        $editForm = $this->createForm(new PacienteType(), $entity);
        
    	$user = $this->get('security.context')->getToken()->getUser();
                
        if ($user->getPerfil() == 'ROLE_AUX') {
        	$plantilla = 'ParametrizarBundle:Paciente:edit.html.twig';
        }
        elseif ($user->getPerfil() == 'ROLE_ADMIN') {
        	$plantilla = 'AdminBundle:Paciente:edit.html.twig';
        } 
    
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Paciente",$this->get("router")->generate("paciente_search"));        
        $breadcrumbs->addItem("Detalle ",$this->get("router")->generate("paciente_show",array("id" => $entity->getId())));
        $breadcrumbs->addItem("Modificar ".$entity->getPriNombre());
        
        return $this->render($plantilla, array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView()
        ));
    }
    
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();    
        $entity = $em->getRepository('ParametrizarBundle:Paciente')->find($id);
    
        if (!$entity) {
            throw $this->createNotFoundException('El paciente solicitado no existe.');
        }
    
        $editForm   = $this->createForm(new PacienteType(), $entity);    
        $request = $this->getRequest();    
        $editForm->bindRequest($request);
    
        if ($editForm->isValid()) {
    
            $em->persist($entity);
            $em->flush();
    
            $this->get('session')->setFlash('ok', 'La paciente ha sido modificado éxitosamente.');    
            return $this->redirect($this->generateUrl('paciente_edit', array('id' => $id)));
        }
        
    	$user = $this->get('security.context')->getToken()->getUser();
                
        if ($user->getPerfil() == 'ROLE_AUX') {
        	$plantilla = 'ParametrizarBundle:Paciente:edit.html.twig';
        }
        elseif ($user->getPerfil() == 'ROLE_ADMIN') {
        	$plantilla = 'AdminBundle:Paciente:edit.html.twig';
        } 
    
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Paciente",$this->get("router")->generate("paciente_search"));
        $breadcrumbs->addItem("Detalle ",$this->get("router")->generate("paciente_show",array("id" => $entity->getId())));
        $breadcrumbs->addItem("Modificar ".$entity->getPriNombre());
        
        return $this->render($plantilla, array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
        ));
    }
    
    
    public function municipiosAction($id) 
    {
        if (is_numeric($_GET["newPaciente_depto"])){
            
            $em = $this->getDoctrine()->getEntityManager();            
            $entity = $em->getRepository('ParametrizarBundle:Mupio')->findBy(array('depto' => $_GET["newPaciente_depto"]));

            
            foreach($entity as $value)
            {
                $response[$value->getId()] = $value->getMunicipio();
            }
            
            if($id) $response["selected"] = $id;
        }
        
        $respuesta = new Response(json_encode($response));
        $respuesta->headers->set('Content-Type', 'application/json');
        
        return $respuesta;
        
    }
    
    
    public function ajaxBuscarAction(){
        
            $request = $this->get('request');
            $id=$request->request->get('id');
            
            if(is_numeric($id)){
            
                $em = $this->getDoctrine()->getEntityManager();
                $entity = $em->getRepository('ParametrizarBundle:Paciente')->findOneBy(array('identificacion' => $id));
                
                if($entity){
                    $cliente = $em->getRepository('ParametrizarBundle:Afiliacion')->findBy(array('paciente' => $entity->getId()));
                                           
                    $response=array("responseCode" => 200,
                                    "id" => $entity->getId(),
                                    "nombre" => $entity->getPriNombre()." ".$entity->getSegNombre()." ".$entity->getPriApellido()." ".$entity->getSegApellido());
                    
                    foreach($cliente as $value)
                    {
                        $response['cliente'][$value->getCliente()->getId()] = $value->getCliente()->getNombre();
                    }                    
                }
                else{
                    $response=array("responseCode"=>400, "msg"=>"el paciente no existe en el sistema!");
                }
            }else{
                $response=array("responseCode"=>400, "msg"=>"Por favor ingrese un valor valido.");
            }
    
        $return=json_encode($response);
        return new Response($return,200,array('Content-Type'=>'application/json'));
    }

}