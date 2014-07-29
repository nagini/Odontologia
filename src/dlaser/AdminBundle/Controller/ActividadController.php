<?php

namespace dlaser\AdminBundle\Controller;

use dlaser\ParametrizarBundle\Entity\Cargo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use dlaser\ParametrizarBundle\Entity\Cliente;
use dlaser\ParametrizarBundle\Entity\Actividad;
use dlaser\AdminBundle\Form\ActividadType;

class ActividadController extends Controller
{
    
    public function newAction($id)
    {
        $entity = new Actividad();
        $form   = $this->createForm(new ActividadType(), $entity);
        
        $em = $this->getDoctrine()->getEntityManager();
        $contrato = $em->getRepository('ParametrizarBundle:Contrato')->find(array('id' => $id));
        
        if (!$contrato) {
        	throw $this->createNotFoundException('El contrato solicitado no existe.');
        }
        $cliente = $contrato->getCliente();
        $sede = $contrato->getSede();
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Cliente Listar",$this->get("router")->generate("cliente_list"));
        $breadcrumbs->addItem("Detalle ".$cliente->getNombre(),$this->get("router")->generate("cliente_show",array("id" => $cliente->getId())));
        $breadcrumbs->addItem("Detalle ".$contrato->getContacto(),$this->get("router")->generate("contrato_show",array("id" => $contrato->getId())));
        $breadcrumbs->addItem("Nueva actividad ");
    
        return $this->render('AdminBundle:Actividad:new.html.twig', array(
                'entity' => $entity,
                'id'    => $id,
        		'sede' => $sede,
        		'contrato' => $contrato,
        		'cliente' => $cliente,
                'form'   => $form->createView()
        ));
    }
    
    public function saveAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();        
        $contrato = $em->getRepository('ParametrizarBundle:Contrato')->find($id);
        
        if (!$contrato) {
            throw $this->createNotFoundException('El contrato dado no existe.');
        }
        
        $entity  = new Actividad();
        $request = $this->getRequest();
        $form    = $this->createForm(new ActividadType(), $entity);
        $form->bindRequest($request);
        
        $cliente = $contrato->getCliente();
        $sede = $contrato->getSede();
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Cliente Listar",$this->get("router")->generate("cliente_list"));
        $breadcrumbs->addItem("Detalle ".$cliente->getNombre(),$this->get("router")->generate("cliente_show",array("id" => $cliente->getId())));
        $breadcrumbs->addItem("Detalle ".$contrato->getContacto(),$this->get("router")->generate("contrato_show",array("id" => $contrato->getId())));
        $breadcrumbs->addItem("Nueva atividad ");
                    
        if ($form->isValid()) {
            
            $actividad = $em->getRepository('ParametrizarBundle:Actividad')->findBy(array('contrato' => $contrato->getId(), 'cargo' => $entity->getCargo()->getId()));
            
            if($actividad){
                $this->get('session')->setFlash('error', 'La actividad ya se encuentra contratada.');              
                
                return $this->render('AdminBundle:Actividad:new.html.twig', array(
	                'entity' => $entity,
	                'id'    => $id,
	        		'sede' => $sede,
	        		'contrato' => $contrato,
	        		'cliente' => $cliente,
	                'form'   => $form->createView()
        		));
            }

            $entity->setContrato($contrato);
            $em->persist($entity);
            $em->flush();
    
            $this->get('session')->setFlash('ok', 'La actividad ha sido creado éxitosamente.');      
            return $this->redirect($this->generateUrl('actividad_show', array("contrato" => $id, "cargo" => $entity->getCargo()->getId())));
    
        }
    
       return $this->render('AdminBundle:Actividad:new.html.twig', array(
                'entity' => $entity,
                'id'    => $id,
        		'sede' => $sede,
        		'contrato' => $contrato,
        		'cliente' => $cliente,
                'form'   => $form->createView()
        ));
    
    }
    
    public function showAction($contrato, $cargo)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $query = $em->createQuery('SELECT a FROM dlaser\ParametrizarBundle\Entity\Actividad a WHERE a.contrato = :contrato AND a.cargo = :cargo');
        $query->setParameters(array(
                'contrato' => $contrato,
                'cargo' => $cargo,
        ));        
        $actividad = $query->getSingleResult();
        
        
    
        if (!$actividad) {
            throw $this->createNotFoundException('La actividad solicitada no existe.');
        }
        $contrato = $em->getRepository('ParametrizarBundle:Contrato')->find($contrato);        
        $cliente = $contrato->getCliente();
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Cliente Listar",$this->get("router")->generate("cliente_list"));
        $breadcrumbs->addItem("Detalle ".$cliente->getNombre(),$this->get("router")->generate("cliente_show",array("id" => $cliente->getId())));
        $breadcrumbs->addItem("Detalle ".$contrato->getContacto(),$this->get("router")->generate("contrato_show",array("id" => $contrato->getId())));
        $breadcrumbs->addItem("Detalle Actividad");
    
        return $this->render('AdminBundle:Actividad:show.html.twig', array(
                'actividad'    => $actividad,
        ));
    }
    
    public function editAction($contrato, $cargo)
    {
        $em = $this->getDoctrine()->getEntityManager();
    
        $query = $em->createQuery('SELECT a FROM dlaser\ParametrizarBundle\Entity\Actividad a WHERE a.contrato = :contrato AND a.cargo = :cargo');
        $query->setParameters(array(
                'contrato' => $contrato,
                'cargo' => $cargo,
        ));
        $actividad = $query->getSingleResult();
    
        if (!$actividad) {
            throw $this->createNotFoundException('La actividad solicitada no existe');
        }
        
        $contrato = $em->getRepository('ParametrizarBundle:Contrato')->find($contrato);
        $cliente = $contrato->getCliente();
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Cliente Listar",$this->get("router")->generate("cliente_list"));
        $breadcrumbs->addItem("Detalle ".$cliente->getNombre(),$this->get("router")->generate("cliente_show",array("id" => $cliente->getId())));
        $breadcrumbs->addItem("Detalle ".$contrato->getContacto(),$this->get("router")->generate("contrato_show",array("id" => $contrato->getId())));
        $breadcrumbs->addItem("Detalle",$this->get("router")->generate("actividad_show",array("contrato" => $contrato->getId(),"cargo" => $cargo)));
        $breadcrumbs->addItem("Modificar actividad");
    
        $editForm = $this->createForm(new ActividadType(), $actividad);
    
        return $this->render('AdminBundle:Actividad:edit.html.twig', array(
                'entity'      => $actividad,
                'edit_form'   => $editForm->createView(),
        ));
    }
    
    public function updateAction($contrato, $cargo)
    {
        $em = $this->getDoctrine()->getEntityManager();
    
        $query = $em->createQuery('SELECT a FROM dlaser\ParametrizarBundle\Entity\Actividad a WHERE a.contrato = :contrato AND a.cargo = :cargo');
        $query->setParameters(array(
                'contrato' => $contrato,
                'cargo' => $cargo,
        ));
        $actividad = $query->getSingleResult();
    
        if (!$actividad) {
            throw $this->createNotFoundException('La actividad solicitada no existe');
        }
    
        $editForm   = $this->createForm(new ActividadType(), $actividad);    
        $request = $this->getRequest();    
        $editForm->bindRequest($request);
    
        if ($editForm->isValid()) {
    
            $em->persist($actividad);
            $em->flush();
    
            $this->get('session')->setFlash('ok', 'La actividad ha sido modificada éxitosamente.');    
            return $this->redirect($this->generateUrl('actividad_edit', array('contrato' => $contrato, 'cargo' => $cargo)));
        }
    
        return $this->render('AdminBundle:Actividad:edit.html.twig', array(
                'entity'      => $actividad,
                'edit_form'   => $editForm->createView(),
        ));
    }

}