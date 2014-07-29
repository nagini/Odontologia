<?php

namespace dlaser\AdminBundle\Controller;;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use dlaser\ParametrizarBundle\Entity\Cliente;
use dlaser\ParametrizarBundle\Entity\Contrato;
use dlaser\AdminBundle\Form\ContratoType;

class ContratoController extends Controller
{
    
    public function newAction($id)
    {
        $entity = new Contrato();
        $form   = $this->createForm(new ContratoType(), $entity);
        
        $em = $this->getDoctrine()->getEntityManager();
        $cliente = $em->getRepository('ParametrizarBundle:Cliente')->find($id);
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Cliente Listar",$this->get("router")->generate("cliente_list"));        
        $breadcrumbs->addItem("Detalle ".$cliente->getNombre(),$this->get("router")->generate("cliente_show",array("id" => $cliente->getId())));
        $breadcrumbs->addItem("Nuevo contrato");
        
    
        return $this->render('AdminBundle:Contrato:new.html.twig', array(
                'entity' => $entity,
                'id'    => $id,
        		'cliente' => $cliente,
                'form'   => $form->createView()
        ));
    }
    
    public function saveAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();        
        $cliente = $em->getRepository('ParametrizarBundle:Cliente')->find($id);
        
        if (!$cliente) {
            throw $this->createNotFoundException('El cliente solicitado no existe.');
        }
        
        $entity  = new Contrato();
        
        $request = $this->getRequest();
        $form    = $this->createForm(new ContratoType(), $entity);
        $form->bindRequest($request);
    
        if ($form->isValid()) {
            
            $contrato = $em->getRepository('ParametrizarBundle:Contrato')->findBy(array('cliente' => $cliente->getId(), 'sede' => $entity->getSede()->getId()));
            
            if($contrato){
            	
                $this->get('session')->setFlash('error', 'Ya existe un contrato con la sede seleccionada.');            
                return $this->render('AdminBundle:Contrato:new.html.twig', array(
                        'entity' => $entity,
                        'id'    => $id,
                		'cliente' => $cliente,
                        'form'   => $form->createView()
                ));
            }                        
            $entity->setCliente($cliente);            
            $em->persist($entity);
            $em->flush();
    
            $this->get('session')->setFlash('ok', 'El contrato ha sido creado éxitosamente.');    
            return $this->redirect($this->generateUrl('contrato_show', array("id" => $entity->getId())));    
        }    
        return $this->render('AdminBundle:Contrato:new.html.twig', array(
                'entity' => $entity,
                'id'    => $id,
        		'cliente' => $cliente,
                'form'   => $form->createView()
        ));
    
    }
    
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $contrato = $em->getRepository('ParametrizarBundle:Contrato')->find(array('id' => $id));
    
        if (!$contrato) {
            throw $this->createNotFoundException('El contrato solicitado no existe.');
        }
        
        $cliente = $contrato->getCliente();
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Cliente Listar",$this->get("router")->generate("cliente_list"));        
        $breadcrumbs->addItem("Detalle ".$cliente->getNombre(),$this->get("router")->generate("cliente_show",array("id" => $cliente->getId())));
        $breadcrumbs->addItem("Detalle ".$contrato->getContacto());
        
        $actividades = $em->getRepository('ParametrizarBundle:Actividad')->findBy(array('contrato' => $id));
    
        return $this->render('AdminBundle:Contrato:show.html.twig', array(
                'contrato'    => $contrato,
                'actividades'    => $actividades,
        ));
    }
    
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();    
        $contrato = $em->getRepository('ParametrizarBundle:Contrato')->find(array('id' => $id));
    
        if (!$contrato) {
            throw $this->createNotFoundException('El contrato solicitado no existe.');
        }
        
        $cliente = $contrato->getCliente();
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Cliente Listar",$this->get("router")->generate("cliente_list"));
        $breadcrumbs->addItem("Detalle ".$cliente->getNombre(),$this->get("router")->generate("cliente_show",array("id" => $cliente->getId())));
        $breadcrumbs->addItem("Detalle ",$this->get("router")->generate("contrato_show",array("id" => $contrato->getId())));
        $breadcrumbs->addItem("Modificar ".$contrato->getContacto());
    
        $editForm = $this->createForm(new ContratoType(), $contrato);
    
        return $this->render('AdminBundle:Contrato:edit.html.twig', array(
                'entity'      => $contrato,
                'edit_form'   => $editForm->createView(),
        ));
    }
    
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();    
        $contrato = $em->getRepository('ParametrizarBundle:Contrato')->find(array('id' => $id));
    
        if (!$contrato) {
            throw $this->createNotFoundException('El contrato solicitado no existe');
        }
    
        $editForm   = $this->createForm(new ContratoType(), $contrato);    
        $request = $this->getRequest();    
        $editForm->bindRequest($request);
    
        if ($editForm->isValid()) {
    
            $em->persist($contrato);
            $em->flush();
    
            $this->get('session')->setFlash('ok', 'La información del contrato ha sido modificada éxitosamente.');    
            return $this->redirect($this->generateUrl('contrato_edit', array('id' => $id)));
        }
    
        return $this->render('AdminBundle:Contrato:edit.html.twig', array(
                'entity'      => $contrato,
                'edit_form'   => $editForm->createView(),
        ));
    }

}