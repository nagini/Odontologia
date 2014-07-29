<?php

namespace dlaser\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * dlaser\UsuarioBundle\Entity\Usuario
 * 
 * @ORM\Table(name="usuario")
 * @ORM\Entity
 */
class Usuario implements UserInterface, \Serializable
{

    /**
     * Método requerido por la interfaz UserInterface
     */
    function equals(\Symfony\Component\Security\Core\User\UserInterface $usuario)
    {
        return $this->getCc() == $usuario->getCc();
    }
    
    /**
     * Método requerido por la interfaz UserInterface
     */
    function eraseCredentials()
    {
    }
    
    /**
     * Método requerido por la interfaz UserInterface
     */
    function getRoles()
    {
        return array($this->getPerfil());
    }
    
    /**
     * Método requerido por la interfaz UserInterface
     */
    function getSalt()
    {
        return null;
    }
    
    /**
     * Método requerido por la interfaz UserInterface
     */
    function getUsername()
    {
        return $this->getNombre();
    }
    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @var integer $cc
     * @ORM\Column(name="cc", type="integer", nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Max(limit = "9999999999999", message = "El valor cc no puede ser mayor de {{ limit }}",invalidMessage = "El valor ingresado debe ser un número válido")
     * 
     */
    private $cc;

    /**
     * @var string $nombre
     * @ORM\Column(name="nombre", type="string", length=60, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\MaxLength(limit=60, message="El valor nombre debe tener como maximo {{ limit }} caracteres.")
     * 
     */
    private $nombre;

    /**
     * @var string $apellido
     * @ORM\Column(name="apellido", type="string", length=60, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\MaxLength(limit=60, message="El valor apellido debe tener como maximo {{ limit }} caracteres.")
     * 
     */
    private $apellido;

    /**
     * @var string $perfil
     * @ORM\Column(name="perfil", type="string", length=13, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\MaxLength(limit=13, message="El valor perfil debe tener como maximo {{ limit }} caracteres.")
     * 
     */
    private $perfil;

    /**
     * @var string $telefono
     * @ORM\Column(name="telefono", type="string", length=11, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Max(limit = "99999999999", message = "El valor telefono no puede ser mayor de {{ limit }}",invalidMessage = "El valor ingresado debe ser un número válido")
     * 
     */
    private $telefono;

    /**
     * @var string $direccion
     *
     * @ORM\Column(name="direccion", type="string", length=60, nullable=true)
     * @Assert\MaxLength(limit=60, message="El valor direccion debe tener como maximo {{ limit }} caracteres.")
     */
    private $direccion;

    /**
     * @var string $tp
     * @ORM\Column(name="tp", type="string", length=11, nullable=true)
     * @Assert\MaxLength(limit=11, message="El valor tp debe tener como maximo {{ limit }} caracteres.")
     * 
     */
    private $tp;

    /**
     * @var string $especialidad
     * @ORM\Column(name="especialidad", type="string", length=30, nullable=true)
     * @Assert\MaxLength(limit=30, message="El valor especialidad debe tener como maximo {{ limit }} caracteres.")
     * 
     */
    private $especialidad;

    /**
     * @var string $password
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     * @Assert\MaxLength(limit=255, message="El valor password debe tener como maximo {{ limit }} caracteres.")
     * 
     */
    private $password;

    /**
     * @var string $email
     * @ORM\Column(name="email", type="string", length=200, nullable=true)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\MaxLength(limit=200, message="El valor password debe tener como maximo {{ limit }} caracteres.")
     * 
     */
    private $email;

    /**
     * @var string $firma
     *
     * @ORM\Column(name="firma", type="string", length=255, nullable=true)
     */
    private $firma;

    

    /**
     * @var Sede
     *
     * @ORM\ManyToMany(targetEntity="dlaser\ParametrizarBundle\Entity\Sede", mappedBy="usuario")
     */
    private $sede;

    public function __construct()
    {        
	    $this->sede = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    
    public function __toString()
    {
        return $this->getNombre()." ".$this->getApellido();
    }
    
    public function serialize()
    {
       return serialize($this->getId());
    }
 
    public function unserialize($data)
    {
        $this->id = unserialize($data);
    }
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set cc
     *
     * @param integer $cc
     */
    public function setCc($cc)
    {
        $this->cc = $cc;
    }

    /**
     * Get cc
     *
     * @return integer 
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }

    /**
     * Get apellido
     *
     * @return string 
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set perfil
     *
     * @param string $perfil
     */
    public function setPerfil($perfil)
    {
        $this->perfil = $perfil;
    }

    /**
     * Get perfil
     *
     * @return string 
     */
    public function getPerfil()
    {
        return $this->perfil;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    /**
     * Get direccion
     *
     * @return string 
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set tp
     *
     * @param string $tp
     */
    public function setTp($tp)
    {
        $this->tp = $tp;
    }

    /**
     * Get tp
     *
     * @return string 
     */
    public function getTp()
    {
        return $this->tp;
    }

    /**
     * Set especialidad
     *
     * @param string $especialidad
     */
    public function setEspecialidad($especialidad)
    {
        $this->especialidad = $especialidad;
    }

    /**
     * Get especialidad
     *
     * @return string 
     */
    public function getEspecialidad()
    {
        return $this->especialidad;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set firma
     *
     * @param string $firma
     */
    public function setFirma($firma)
    {
        $this->firma = $firma;
    }

    /**
     * Get firma
     *
     * @return string 
     */
    public function getFirma()
    {
        return $this->firma;
    }    

    /**
     * Add sede
     *
     * @param dlaser\ParametrizarBundle\Entity\Sede $sede
     */
    public function addSede(\dlaser\ParametrizarBundle\Entity\Sede $sede)
    {
        
        if (!$this->hasSede($sede)) {
        	$this->sede[] = $sede;
        	return true;
        }
        return false;
    }
    
    
    public function hasSede(\dlaser\ParametrizarBundle\Entity\Sede $sede)
    {
    	foreach ($this->sede as $value) {
    		if ($value->getId() == $sede->getId()) {
    			return true;
    		}
    	}
    	return false;
    }
    
    

    /**
     * Get sede
     *
     * @return Doctrine\Common\Collections\Collection $sede 
     */
    public function getSede()
    {
        return $this->sede;
    }
}
