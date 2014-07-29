<?php

namespace dlaser\AdminBundle\Controller;;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use dlaser\ParametrizarBundle\Entity\Sede;
use dlaser\ParametrizarBundle\Entity\Empresa;
use dlaser\AdminBundle\Form\SedeType;

class SedeController extends Controller
{
    
    public function newAction($id)
    {
        $entity = new Sede();
        $form   = $this->createForm(new SedeType(), $entity);
        
        $em = $this->getDoctrine()->getEntityManager();
        $empresa = $em->getRepository('ParametrizarBundle:Empresa')->find($id);
        
        if (!$empresa) {
        	throw $this->createNotFoundException('La empresa solicitada no esta disponible.');
        }
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Empresa", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Listar", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Detalle ".$empresa->getNombre(), $this->get("router")->generate("empresa_show", array("id" => $empresa->getId())));        
        $breadcrumbs->addItem("Sede Nueva");

        return $this->render('AdminBundle:Sede:new.html.twig', array(
            'entity' => $entity,
            'empresa' => $id,
            'form'   => $form->createView()
        ));
    }
    
    public function saveAction($id)
    {
        $entity  = new Sede();
        $request = $this->getRequest();
        $form    = $this->createForm(new SedeType(), $entity);
        $form->bindRequest($request);
        
        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getEntityManager();
            $empresa = $em->getRepository('ParametrizarBundle:Empresa')->find($id);
            
            if (!$empresa) {
                throw $this->createNotFoundException('La empresa solicitada no existe');
            }
            
            $entity->setEmpresa($empresa);
            	
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();
            
            $this->get('session')->setFlash('ok', 'La sede ha sido creada éxitosamente.');  
        
            return $this->redirect($this->generateUrl('empresa_show', array("id" => $id)));
        
        }
        
        return $this->render('AdminBundle:Sede:new.html.twig', array(
            'entity' => $entity,
            'empresa' => $id,
            'form'   => $form->createView()
        ));
    }
    
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();    
        $sede = $em->getRepository('ParametrizarBundle:Sede')->find($id);       
    
        if (!$sede) {
            throw $this->createNotFoundException('La sede solicitada no esta disponible.');
        }
        $empresa = $sede->getEmpresa();
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Empresa", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Listar", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Detalle ".$empresa->getNombre(), $this->get("router")->generate("empresa_show", array("id" => $empresa->getId())));
        $breadcrumbs->addItem("Detalle ".$sede->getNombre());
    
        return $this->render('AdminBundle:Sede:show.html.twig', array(
                'entity'  => $sede,
                'empresa' => $empresa,
        ));
    }
    
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();    
        $sede = $em->getRepository('ParametrizarBundle:Sede')->find($id);
    
        if (!$sede) {
            throw $this->createNotFoundException('La sede solicitada no existe');
        }    
        $empresa = $sede->getEmpresa();
        
        $editForm = $this->createForm(new SedeType(), $sede);
        $deleteForm = $this->createDeleteForm($id);
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Empresa", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Listar", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Detalle ".$empresa->getNombre(), $this->get("router")->generate("empresa_show", array("id" => $empresa->getId())));
        $breadcrumbs->addItem("Detalle ", $this->get("router")->generate("sede_show", array("id" => $sede->getId())));
        $breadcrumbs->addItem("Modificar ".$sede->getNombre());
    
        return $this->render('AdminBundle:Sede:edit.html.twig', array(
                'entity'      => $sede,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
        ));
    }
    
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();    
        $entity = $em->getRepository('ParametrizarBundle:Sede')->find($id);
    
        if (!$entity) {
            throw $this->createNotFoundException('La sede solicitada no existe.');
        }
    
        $editForm   = $this->createForm(new SedeType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
    
        $request = $this->getRequest();    
        $editForm->bindRequest($request);
    
        if ($editForm->isValid()) {
    
            $em->persist($entity);
            $em->flush();
    
            $this->get('session')->setFlash('ok', 'La sede ha sido modificada éxitosamente.');    
            return $this->redirect($this->generateUrl('sede_edit', array('id' => $id)));
        }
    
        return $this->render('AdminBundle:Sede:edit.html.twig', array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
        ));
    }
    
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();
    
        $form->bindRequest($request);
    
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('ParametrizarBundle:Sede')->find($id);
    
            if (!$entity) {
                throw $this->createNotFoundException('La sede solicitada no se encuentra disponible.');
            }
            
            $empresa = $entity->getEmpresa()->getId();
    
            $em->remove($entity);
            $em->flush();
            
            $this->get('session')->setFlash('ok', 'La sede ha sido eliminada éxitosamente.');            
            return $this->redirect($this->generateUrl('empresa_show', array('id' => $empresa)));
        }
        
        $editForm = $this->createForm(new SedeType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        
        return $this->render('AdminBundle:Sede:edit.html.twig', array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
        ));
    
        
    }
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
        ->add('id', 'hidden')
        ->getForm()
        ;
    }
    
    
    /**
     * @uses Función que devuelve las sedes asociadas de un usuario. 
     * 
     * @param ninguno
     */
    public function ajaxSedeUsuarioAction() {
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        $id=$user->getId();
        
        $usuario = $em->getRepository('UsuarioBundle:Usuario')->find($id);        
        
        if(!$usuario)
        {
            throw $this->createNotFoundException('El usuario solicitado no existe');
        }
        
        $sedes = $usuario->getSede();
        
        if($sedes){
            $response=array("responseCode" => 200);
        
            foreach($sedes as $value)
            {
                $response['sedes'][$value->getId()] = $value->getNombre();
            }
        }else {
            $response=array("responseCode"=>400, "msg"=>"El usuario no tiene ninguna sede asociada!");            
        }
        
        $return=json_encode($response);
        return new Response($return,200,array('Content-Type'=>'application/json'));        
        
    }
    
}