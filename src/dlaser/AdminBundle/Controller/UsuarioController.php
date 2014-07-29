<?php
namespace dlaser\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use dlaser\UsuarioBundle\Entity\Usuario;
use dlaser\ParametrizarBundle\Entity\Sede;
use dlaser\AdminBundle\Form\UsuarioType;


class UsuarioController extends Controller
{

    public function loginAction()
    {
        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();
    
        $error = $peticion->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR,
                $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
        );
    
        return $this->render('AdminBundle:Default:login.html.twig', array(
                'last_username' => $sesion->get(SecurityContext::LAST_USERNAME),
                'error'         => $error
        ));

    }
    
    public function auxLoginAction()
    {
        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();
    
        $error = $peticion->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR,
                $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
        );
    
        return $this->render('AdminBundle:Default:auxLogin.html.twig', array(
                'last_username' => $sesion->get(SecurityContext::LAST_USERNAME),
                'error'         => $error
        ));
    }
    
    public function listAction()
	{
		$em = $this->getDoctrine()->getEntityManager();		
		$usuario = $em->getRepository('UsuarioBundle:Usuario')->findAll();
		
		$breadcrumbs = $this->get("white_october_breadcrumbs");
		$breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));		
		$breadcrumbs->addItem("Usuario", $this->get("router")->generate("usuario_list"));
		$breadcrumbs->addItem("Listar");
		
		return $this->render('AdminBundle:Usuario:list.html.twig', array('entities' => $usuario)); 
	}
	
	public function newAction()
	{
		$entity = new Usuario();
		$form   = $this->createForm(new UsuarioType(), $entity);
		
		$breadcrumbs = $this->get("white_october_breadcrumbs");
		$breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
		$breadcrumbs->addItem("Usuario", $this->get("router")->generate("usuario_list"));
		$breadcrumbs->addItem("Nuevo");
		
		return $this->render('AdminBundle:Usuario:new.html.twig', array(
				'entity' => $entity,
				'form'   => $form->createView()
		));
	}
	
	public function saveAction()
	{
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getEntityManager();
		
		$entity  = new Usuario();		
		$form    = $this->createForm(new UsuarioType(), $entity);
		
		if($request->getMethod() == 'POST')
		{
			$form->bindRequest($request);
			
			if ($form->isValid()) {				
				
				if($form->getData()->getPassword()){
					$factory = $this->get('security.encoder_factory');
					$codificador = $factory->getEncoder($entity);
					$password = $codificador->encodePassword($entity->getPassword(), $entity->getSalt());
					$entity->setPassword($password);
						
					$em->persist($entity);
					$em->flush();
					
					$this->get('session')->setFlash('ok','El usuario se ha creado éxitosamente');
					return $this->redirect($this->generateUrl('usuario_list'));					
				}else{
					$this->get('session')->setFlash('error','El usuario debe ingresar una contraseña.');
					return $this->render('AdminBundle:Usuario:new.html.twig', array(
							'entity' => $entity,
							'form'   => $form->createView()
					));
				}
			}
		}				
		return $this->render('AdminBundle:Usuario:new.html.twig', array(
				'entity' => $entity,
				'form'   => $form->createView()
		));
	}
	
	public function showAction($id)
	{
		$em = $this->getDoctrine()->getEntityManager();		
		$usuario = $em->getRepository('UsuarioBundle:Usuario')->find($id);
				
		if(!$usuario)
		{
			throw $this->createNotFoundException('El usuario solicitado no existe');
		}
		
		$sedes = $usuario->getSede();
		
		$breadcrumbs = $this->get("white_october_breadcrumbs");
		$breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
		$breadcrumbs->addItem("Usuario", $this->get("router")->generate("usuario_list"));		
		$breadcrumbs->addItem("Detalle ".$usuario->getNombre());
						
		return $this->render('AdminBundle:Usuario:show.html.twig', array(
				'entity'  => $usuario,
				'sedes'    => $sedes
		));
	}
	
	public function editAction($id)
	{
		$em = $this->getDoctrine()->getEntityManager();		
		$usuario = $em->getRepository('UsuarioBundle:Usuario')->find($id);
	  	
		if(!$usuario)
		{
			throw $this->createNotFoundException('El usuario solicitado no existe');
		}		
		$editform   = $this->createForm(new UsuarioType(), $usuario);
		
		$breadcrumbs = $this->get("white_october_breadcrumbs");
		$breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
		$breadcrumbs->addItem("Usuario", $this->get("router")->generate("usuario_list"));		
		$breadcrumbs->addItem("Detalle ", $this->get("router")->generate("usuario_show",array("id" => $usuario->getId())));
		$breadcrumbs->addItem("Modificar ".$usuario->getNombre());
		
		return $this->render('AdminBundle:Usuario:edit.html.twig', array(
				'entity' => $usuario,
				'edit_form'   => $editform->createView()
		));
		
	}
	
    public function updateAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$entity = $em->getRepository('UsuarioBundle:Usuario')->find($id);
    	
    	if(!$entity)
    	{
    		throw $this->createNotFoundException('El usuario solicitado no existe.');
    	}
    	
    	$upForm = $this->createForm(new UsuarioType(), $entity);
    	$request = $this->getRequest();
    	
    	$passwdOriginal = $upForm->getData()->getPassword();
    	
    	$upForm->bindRequest($request);    	
    	
    	if ($upForm->isValid()) 
    	{
    	     if (null == $entity->getPassword()) {
                 $entity->setPassword($passwdOriginal);
             }
             // Si el usuario ha cambiado su password, hay que codificarlo antes de guardarlo
             else {                   		
			     // Codificamos el password
		         $factory = $this->get('security.encoder_factory');
		         $codificador = $factory->getEncoder($entity);
		         $password = $codificador->encodePassword($entity->getPassword(), $entity->getSalt());
		         $entity->setPassword($password);
            }  		    	
    		
			$em->persist($entity);
    		$em->flush();    	
    		
    		$this->get('session')->setFlash('ok', 'El usuario ha sido modificado éxitosamente.');    	
    		return $this->redirect($this->generateUrl('usuario_edit', array('id' => $id)));
    	}
    	
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
    	$breadcrumbs->addItem("Usuario", $this->get("router")->generate("usuario_list"));
    	$breadcrumbs->addItem("Detalle ", $this->get("router")->generate("usuario_show",array("id" => $entity->getId())));
    	$breadcrumbs->addItem("Modificar ".$entity->getNombre());
    	
    	return $this->render('AdminBundle:Usuario:edit.html.twig', array(
    			'entity'      => $entity,
    			'edit_form'   => $upForm->createView(),

    	));    	
    }
    
    public function newPermisoAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();    	
    	$usuario = $em->getRepository('UsuarioBundle:Usuario')->find($id);    	
    	    	    	     	
    	if (!$usuario){
    		throw $this->createNotFoundException('El usuario solicitado no existe');
    	}
    	
    	$permisos = $usuario->getSede();
    	
    	if($permisos){
    		
    		$dql = $em->createQuery('SELECT s FROM ParametrizarBundle:Sede s
    				WHERE s.id NOT IN (SELECT S FROM ParametrizarBundle:Sede S JOIN S.usuario u JOIN u.sede se WHERE u.id = :id)');
    		
    		$dql->setParameter('id', $id);    		 
    		$consulta = $dql->getResult();    		
    		
    	}else{
    		$consulta = $em->getRepository('ParametrizarBundle:Sede')->findAll();;
    		$permisos = 0;
    	}    	    	

    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
    	$breadcrumbs->addItem("Usuario", $this->get("router")->generate("usuario_list"));    	
    	$breadcrumbs->addItem("Detalle ", $this->get("router")->generate("usuario_show",array("id" => $usuario->getId())));
    	$breadcrumbs->addItem("Permiso ".$usuario->getNombre());
    	
    	return $this->render('AdminBundle:Usuario:newPermiso.html.twig', array(
    			'entity' => $usuario,
    			'sedes'   => $consulta,
    			'permisos' => $permisos,
    	));
    }
    
    public function savePermisoAction($usuario, $sede)
    {
    	$em = $this->getDoctrine()->getEntityManager();		
		$entity = $em->getRepository('UsuarioBundle:Usuario')->find($usuario);
		$entitySede = $em->getRepository('ParametrizarBundle:Sede')->find($sede);
					
		if(!$entity || !$entitySede)
		{
			throw $this->createNotFoundException('El usuario o la sede solicitada no existe');
		}
		
		if($entitySede->addUsuario($entity)){
		    	
    		$em->persist($entitySede);    		
    		$em->flush();
		}

		$this->get('session')->setFlash('ok', 'El permiso ha sido creado éxitosamente.');
    	return $this->redirect($this->generateUrl('permiso_new', array('id' => $usuario)));    	
    }
    
    public function deletePermisoAction($usuario, $sede)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$usuarios = $em->getRepository("UsuarioBundle:Usuario")->findOneById($usuario);
    	$sedes = $em->getRepository("ParametrizarBundle:Sede")->findOneById($sede);
    	   	    	  	
    	$key = $sedes->getUsuario()->indexOf($usuarios);    	    	    	
    	
    	$sedes->getUsuario()->remove($key);
    		    		   		    	    
    	$em->flush();    	    	
    	    	
    	$this->get('session')->setFlash('ok', 'El permiso ha sido eliminado éxitosamente.');
    	return $this->redirect($this->generateUrl('permiso_new', array('id' => $usuario)));
    }
}

