<?php

namespace dlaser\AdminBundle\Controller;;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use dlaser\ParametrizarBundle\Entity\Cliente;
use dlaser\ParametrizarBundle\Entity\Cargo;
use dlaser\AdminBundle\Form\CargoType;

class CargoController extends Controller
{

    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $paginador = $this->get('ideup.simple_paginator');
        $paginador->setItemsPerPage(20);
        
        $dql = $em->createQuery('SELECT c FROM ParametrizarBundle:Cargo c ORDER BY c.nombre ASC');
        $cargos = $paginador->paginate($dql->getResult())->getResult();
                
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Cargo", $this->get("router")->generate("cargo_list"));
        $breadcrumbs->addItem("Listar");

        return $this->render('AdminBundle:Cargo:list.html.twig', array(
                'entities'  => $cargos
        ));
    }
    
    public function newAction()
    {
        $entity = new Cargo();
        $form   = $this->createForm(new CargoType(), $entity);
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Cargo", $this->get("router")->generate("cargo_list"));        
        $breadcrumbs->addItem("Nuevo");
    
        return $this->render('AdminBundle:Cargo:new.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView()
        ));
    }
    
    public function saveAction()
    {
        $entity  = new Cargo();
        $request = $this->getRequest();
        $form    = $this->createForm(new CargoType(), $entity);
        $form->bindRequest($request);
    
        if ($form->isValid()) {
             
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();            
            
            $this->get('session')->setFlash('ok', 'El cargo ha sido creado éxitosamente.');    
            return $this->redirect($this->generateUrl('cargo_show', array("id" => $entity->getId())));    
        }
    
        return $this->render('AdminBundle:Cargo:new.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView()
        ));
    
    }
    
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();    
        $cargo = $em->getRepository('ParametrizarBundle:Cargo')->find($id);
    
        if (!$cargo) {
            throw $this->createNotFoundException('El cargo solicitado no existe.');
        }
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Cargo", $this->get("router")->generate("cargo_list"));        
        $breadcrumbs->addItem("Detalle ".$cargo->getNombre());        
            
        return $this->render('AdminBundle:Cargo:show.html.twig', array(
                'entity'  => $cargo,
        ));
    }
    
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();    
        $cargo = $em->getRepository('ParametrizarBundle:Cargo')->find($id);
    
        if (!$cargo) {
            throw $this->createNotFoundException('El cargo solicitado no existe');
        }
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Cargo", $this->get("router")->generate("cargo_list"));        
        $breadcrumbs->addItem("Detalle ",$this->get("router")->generate("cargo_show",array("id" => $cargo->getId())));
        $breadcrumbs->addItem("Modificar ".$cargo->getNombre());
    
        $editForm = $this->createForm(new CargoType(), $cargo);
    
        return $this->render('AdminBundle:Cargo:edit.html.twig', array(
                'entity'      => $cargo,
                'edit_form'   => $editForm->createView(),
        ));
    }
    
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();    
        $entity = $em->getRepository('ParametrizarBundle:Cargo')->find($id);
    
        if (!$entity) {
            throw $this->createNotFoundException('El cargo solicitado no existe.');
        }
    
        $editForm   = $this->createForm(new CargoType(), $entity);    
        $request = $this->getRequest();    
        $editForm->bindRequest($request);
    
        if ($editForm->isValid()) {
    
            $em->persist($entity);
            $em->flush();
    
            $this->get('session')->setFlash('ok', 'El cargo ha sido modificado éxitosamente.');    
            return $this->redirect($this->generateUrl('cargo_edit', array('id' => $id)));
        }
    
        return $this->render('AdminBundle:Cargo:edit.html.twig', array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
        ));
    }
        
    
    public function ajaxBuscarAction(){
    
        $request = $this->get('request');
        $cliente=$request->request->get('cliente');
        $sede=$request->request->get('sede');
    
        if(is_numeric($cliente) && is_numeric($sede)){
            
            $em = $this->getDoctrine()->getEntityManager();
            $contrato = $em->getRepository('ParametrizarBundle:Contrato')->findOneBy(array('cliente' => $cliente, 'sede' => $sede));
            
            if($contrato){
                
                $cargo = $em->getRepository('ParametrizarBundle:Actividad')->findBy(array('contrato' => $contrato->getId()));
                
                    if($cargo){                       
                 
                        $response=array("responseCode"=>200);
            
                        foreach($cargo as $value)
                        {
                            $response['cargo'][$value->getCargo()->getId()] = $value->getCargo()->getNombre();
                        }
                    }else{
                        $response=array("responseCode"=>400, "msg"=>"El cliente seleccionado no tiene cargos asociados");
                    }
    
            }
            else{
                $response=array("responseCode"=>400, "msg"=>"El cliente seleccionado no tiene contrato con la sede");
            }
        }else{
            $response=array("responseCode"=>400, "msg"=>"Por favor ingrese un valor valido.");
        }
    
        $return=json_encode($response);
        return new Response($return,200,array('Content-Type'=>'application/json'));
    }

}