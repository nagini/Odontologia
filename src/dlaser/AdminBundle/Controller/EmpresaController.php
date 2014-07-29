<?php

namespace dlaser\AdminBundle\Controller;;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use dlaser\ParametrizarBundle\Entity\Empresa;
use dlaser\AdminBundle\Form\EmpresaType;

class EmpresaController extends Controller
{
    
    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();            
        $empresas = $em->getRepository('ParametrizarBundle:Empresa')->findAll();
                
        $breadcrumbs = $this->get("white_october_breadcrumbs");        
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));        
        $breadcrumbs->addItem("Empresa", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Listar");
        
        return $this->render('AdminBundle:Empresa:list.html.twig', array(
                'entities'  => $empresas
        ));
    }
    
    public function newAction()
    {
        $entity = new Empresa();        
        $validator = $this->get('validator');               
        $form   = $this->createForm(new EmpresaType(), $entity);
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");        
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));        
        $breadcrumbs->addItem("Empresa", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Nueva");
    
        return $this->render('AdminBundle:Empresa:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    public function saveAction()
    {
        $entity  = new Empresa();
        $request = $this->getRequest();
        $form    = $this->createForm(new EmpresaType(), $entity);
        $form->bindRequest($request);
        
        if ($form->isValid()) {
            	
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();
            
            $this->get('session')->setFlash('ok', 'La empresa ha sido creada éxitosamente.');        

            return $this->redirect($this->generateUrl('empresa_show', array("id" => $entity->getId())));        
        }
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Empresa", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Nueva");
        
        return $this->render('AdminBundle:Empresa:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));

    }
    
    
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
    
        $empresa = $em->getRepository('ParametrizarBundle:Empresa')->find($id);
        
        if (!$empresa) {
            throw $this->createNotFoundException('La empresa solicitada no esta disponible.');
        }
        
        $sede= $em->getRepository('ParametrizarBundle:Sede')->findByEmpresa($id);
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");        
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Empresa", $this->get("router")->generate("empresa_list"));        
        $breadcrumbs->addItem("Detalle de ".$empresa->getNombre());
    
        return $this->render('AdminBundle:Empresa:show.html.twig', array(
                'entity'  => $empresa,
                'sedes'    => $sede,
        ));
    }
    
    
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
    
        $entity = $em->getRepository('ParametrizarBundle:Empresa')->find($id);
    
        if (!$entity) {
            throw $this->createNotFoundException('La empresa solicitada no existe');
        }
    
        $editForm = $this->createForm(new EmpresaType(), $entity);
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");        
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Empresa", $this->get("router")->generate("empresa_list"));        
        $breadcrumbs->addItem("Detalle", $this->get("router")->generate("empresa_show", array("id" => $entity->getId())));
        $breadcrumbs->addItem("Modificar ".$entity->getNombre());
    
        return $this->render('AdminBundle:Empresa:edit.html.twig', array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
        ));
    }
    
    
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
    
        $entity = $em->getRepository('ParametrizarBundle:Empresa')->find($id);
    
        if (!$entity) {
            throw $this->createNotFoundException('La empresa solicitada no existe.');
        }
    
        $editForm   = $this->createForm(new EmpresaType(), $entity);      
        $request = $this->getRequest();    
        $editForm->bindRequest($request);
    
        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            $this->get('session')->setFlash('ok', 'La empresa ha sido modificada éxitosamente.');    
            return $this->redirect($this->generateUrl('empresa_edit', array('id' => $id)));
        }
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Empresa", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Detalle", $this->get("router")->generate("empresa_show", array("id" => $entity->getId())));
        $breadcrumbs->addItem("Modificar ".$entity->getNombre());
    
        return $this->render('AdminBundle:Empresa:edit.html.twig', array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
        ));
    }
}
