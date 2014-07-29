<?php

namespace dlaser\AgendaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use dlaser\AgendaBundle\Entity\Restriccion;
use dlaser\AgendaBundle\Form\RestriccionType;


class RestriccionController extends Controller
{   
    public function newAction($id)
    {
        $entity = new Restriccion();
        $form   = $this->createForm(new RestriccionType(), $entity);
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Detalle",$this->get("router")->generate("agenda_show",array("id" => $id)));
        $breadcrumbs->addItem("Nueva restriccíon");
    
        return $this->render('AgendaBundle:Restriccion:new.html.twig', array(
                'entity' => $entity,
                'id' => $id,
                'form'   => $form->createView()
        ));
    }    
    
    public function saveAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();        
        
        $agenda = $em->getRepository('AgendaBundle:Agenda')->find($id);
        
        if (!$agenda) {
            throw $this->createNotFoundException('La agenda solicitada no existe.');
        }
                
        $entity  = new Restriccion();        
        $request = $this->getRequest();
        $form    = $this->createForm(new RestriccionType(), $entity);
        $form->bindRequest($request);
    
        if ($form->isValid()) {
    
            $entity->setAgenda($agenda);
            $em->persist($entity);
            $em->flush();
    
            $this->get('session')->setFlash('ok', 'La restricción ha sido creada éxitosamente.');
    
            return $this->redirect($this->generateUrl('agenda_show', array("id" => $agenda->getId())));
    
        }       
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Detalle",$this->get("router")->generate("agenda_show",array("id" => $id)));
        $breadcrumbs->addItem("Nueva restriccíon");
        
        return $this->render('AgendaBundle:Restriccion:new.html.twig', array(
                'entity' => $entity,
                'id' => $id,
                'form'   => $form->createView()
        ));
    
    }
    
    
    public function editAction($id, $agenda)
    {
        $em = $this->getDoctrine()->getEntityManager();    
        $entity = $em->getRepository('AgendaBundle:Restriccion')->find($id);
    
        if (!$entity) {
            throw $this->createNotFoundException('La restricción solicitada no existe');
        }
    
        $editForm = $this->createForm(new RestriccionType(), $entity);
    
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Detalle",$this->get("router")->generate("agenda_show",array("id" => $agenda)));
        $breadcrumbs->addItem("Modificar restriccíon");
        
        return $this->render('AgendaBundle:Restriccion:edit.html.twig', array(
                'entity'      => $entity,
                'agenda'      => $agenda,
                'edit_form'   => $editForm->createView()
        ));
    }
    
    public function updateAction($id, $agenda)
    {
        $em = $this->getDoctrine()->getEntityManager();    
        $entity = $em->getRepository('AgendaBundle:Restriccion')->find($id);
    
        if (!$entity) {
            throw $this->createNotFoundException('La restricción solicitada no existe.');
        }
    
        $editForm = $this->createForm(new RestriccionType(), $entity);    
        $request = $this->getRequest();    
        $editForm->bindRequest($request);
    
        if ($editForm->isValid()) {
    
            $em->persist($entity);
            $em->flush();
    
            $this->get('session')->setFlash('ok', 'La  restricción ha sido modificada éxitosamente.');    
            return $this->redirect($this->generateUrl('agenda_show', array('id' => $agenda)));
        }
    
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Detalle",$this->get("router")->generate("agenda_show",array("id" => $agenda)));
        $breadcrumbs->addItem("Modificar restriccíon");
        
        return $this->render('AgendaBundle:Restriccion:edit.html.twig', array(
                'entity'      => $entity,
                'agenda'      => $agenda,
                'edit_form'   => $editForm->createView()
        ));
    }
    
    
    public function deleteAction($id, $agenda)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('AgendaBundle:Restriccion')->find($id);
    
        if (!$entity) {
            throw $this->createNotFoundException('La restricción a eliminar no existe.');
        }
    
        $em->remove($entity);
        $em->flush();
        
        $this->get('session')->setFlash('info', 'La información de la restricción ha sido eliminada éxitosamente.');
    
        return $this->redirect($this->generateUrl('agenda_show', array("id" => $agenda)));
    }
}