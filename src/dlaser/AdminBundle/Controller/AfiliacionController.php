<?php

namespace dlaser\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use dlaser\ParametrizarBundle\Entity\Cliente;
use dlaser\ParametrizarBundle\Entity\Paciente;
use dlaser\ParametrizarBundle\Entity\Afiliacion;
use dlaser\AdminBundle\Form\AfiliacionType;

class AfiliacionController extends Controller
{
    
    public function saveAction($paciente)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $paciente = $em->getRepository('ParametrizarBundle:Paciente')->find($paciente);
        
        if (!$paciente) {
            throw $this->createNotFoundException('El paciente solicitado no existe.');
        }
        
        $entity  = new Afiliacion();
        $request = $this->getRequest();
        $form    = $this->createForm(new AfiliacionType(), $entity);
        $form->bindRequest($request);
    
        if ($form->isValid()) {

            $afiliacion = $em->getRepository('ParametrizarBundle:Afiliacion')->findBy(array('cliente' => $entity->getCliente()->getId(), 'paciente' => $paciente->getId()));
            
            if($afiliacion){
            	
                $this->get('session')->setFlash('info', 'La asociación ya existe.');                
                return $this->redirect($this->generateUrl('paciente_show', array("id" => $paciente->getId())));
            }
            
            $entity->setPaciente($paciente);
            $em->persist($entity);
            $em->flush();
    
            $this->get('session')->setFlash('ok', 'La asociación ha sido registrada éxitosamente.');    
            return $this->redirect($this->generateUrl('paciente_show', array("id" => $paciente->getId())));
    
        }
    
        return $this->render('AdminBundle:Paciente:show.html.twig', array(
                'paciente' => $paciente,
                'form'   => $form->createView()
        ));
    
    }
    
    
    public function deleteAction($paciente, $cliente)
    {
        
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('ParametrizarBundle:Afiliacion')->find(array('cliente' => $cliente, 'paciente' => $paciente));
    
            if (!$entity) {
                throw $this->createNotFoundException('La asociación a eliminar no existe.');
            }
    
            $em->remove($entity);
            $em->flush();

            $this->get('session')->setFlash('ok', 'La asociación ha sido eliminada éxitosamente.');    
            return $this->redirect($this->generateUrl('paciente_show', array("id" => $paciente)));
    }
    
    
    public function ajaxSaveAction()
    {
    	$request = $this->get('request');
    	
    	$paciente = $request->request->get('paciente');
    	$cliente = $request->request->get('cliente');

    	if($paciente && $cliente){
    		$em = $this->getDoctrine()->getEntityManager();
    		    		    		
    		$paciente = $em->getRepository('ParametrizarBundle:Paciente')->findOneBy(array('identificacion' => $paciente));
    		$cliente = $em->getRepository('ParametrizarBundle:Cliente')->find($cliente);

    		if($paciente && $cliente){
    			
    			$afiliacion = $em->getRepository('ParametrizarBundle:Afiliacion')->findBy(array('cliente' => $cliente->getId(), 'paciente' => $paciente->getId()));

    			if($afiliacion){
    				$response=array("responseCode"=>400, "msg"=>"El cliente ya se encuentra asociado al paciente.");
    			}else{
    				$entity  = new Afiliacion();
    				 
    				$entity->setPaciente($paciente);
    				$entity->setCliente($cliente);
    				$em->persist($entity);
    				$em->flush();
    				 
    				$response=array("responseCode"=>200, "msg"=>"El cliente ha sido agregado correctamente.");
    				 
    				$response['id']= $entity->getCliente()->getId();
    				$response['nombre']= $entity->getCliente()->getNombre();
    			}
    		}else{
    			$response=array("responseCode"=>400, "msg"=>"El cliente ingresado aun no existe en el sistema.");
    		}
    	}
    	
    	$return=json_encode($response);
    	return new Response($return,200,array('Content-Type'=>'application/json'));
    }
    	 
}