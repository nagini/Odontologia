<?php

namespace dlaser\ParametrizarBundle\Entity; 

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * dlaser\ParametrizarBundle\Entity\Sede
 *
 * @ORM\Table(name="sede")
 * @ORM\Entity
 */
class Sede
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=80, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\MinLength(limit=2, message="El valor ingresado debe tener al menos {{ limit }} caracteres.")
     */
    private $nombre;

    /**
     * @var string $ciudad
     * 
     * @ORM\Column(name="ciudad", type="string", length=60, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\MinLength(limit=4, message="El valor ingresado debe tener al menos {{ limit }} caracteres.")
     * @Assert\MaxLength(limit=60, message="El valor ingresado debe tener máximo {{ limit }} caracteres.")
     */
    private $ciudad;

    /**
     * @var integer $telefono
     * 
     * @ORM\Column(name="telefono", type="string", length=7, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Min(limit = "1000000", message = "El valor ingresado no puede ser menor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un n��mero v��lido")
     * @Assert\Max(limit = "9999999", message = "El valor ingresado no puede ser mayor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un n��mero v��lido")
     */
    private $telefono;

    /**
     * @var string $movil
     * 
     * @ORM\Column(name="movil", type="string", nullable=true)
     * @Assert\Min(limit = "3000000000", message = "El valor ingresado no puede ser menor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un n��mero v��lido")
     * @Assert\Max(limit = "9999999999", message = "El valor ingresado no puede ser mayor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un n��mero v��lido")
     */
    private $movil;

    /**
     * @var string $direccion
     * 
     * @ORM\Column(name="direccion", type="string", length=80, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\MinLength(limit=4, message="El valor ingresado debe tener al menos {{ limit }} caracteres.")
     * @Assert\MaxLength(limit=80, message="El valor ingresado debe tener m��ximo {{ limit }} caracteres.")
     */
    private $direccion;

    /**
     * @var string $email
     * 
     * @ORM\Column(name="email", type="string", length=200, nullable=false)
     * @Assert\Email(message = "El email '{{ value }}' no es valido.", checkMX = true)
     */
    private $email;

    /**
     * @var Usuario
     *
     * @ORM\ManyToMany(targetEntity="dlaser\UsuarioBundle\Entity\Usuario", inversedBy="sede")
     * @ORM\JoinTable(name="sede_usuario",
     *   joinColumns={
     *     @ORM\JoinColumn(name="sede_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     *   }
     * )
     */
    private $usuario;

    /**
     * @var Empresa
     *
     * @ORM\ManyToOne(targetEntity="dlaser\ParametrizarBundle\Entity\Empresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="empresa_id", referencedColumnName="id")
     * })
     */
    private $empresa;

    public function __construct()
    {
        $this->usuario = new \Doctrine\Common\Collections\ArrayCollection();
        $this->empresa = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set ciudad
     *
     * @param string $ciudad
     */
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;
    }
    
    /**
     * Get ciudad
     *
     * @return string
     */
    public function getCiudad()
    {
        return $this->ciudad;
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
     * Set movil
     *
     * @param string $movil
     */
    public function setMovil($movil)
    {
        $this->movil = $movil;
    }
    
    /**
     * Get movil
     *
     * @return string
     */
    public function getMovil()
    {
        return $this->movil;
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
     * Add Usuario
     *
     * @param dlaser\UsuarioBundle\Entity\Usuario $usuario
     */
    public function addUsuario(\dlaser\UsuarioBundle\Entity\Usuario $usuario)
    {
        if (!$this->hasUsuario($usuario)) {
        	$this->usuario[] = $usuario;
        	return true;
        }
        return false;
    }
    
    public function hasUsuario(\dlaser\UsuarioBundle\Entity\Usuario $usuario)
    {
    	foreach ($this->usuario as $value) {
    		if ($value->getId() == $usuario->getId()) {
    			return true;
    		}
    	}
    	return false;
    }
    
    /**
     * Get Usuario
     *
     * @return Doctrine\Common\Collections\Collection $usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }   
     
    /**
     * Set Empresa
     *
     * @param dlaser\ParametrizarBundle\Entity\Empresa $empresa
     */
    public function setEmpresa(\dlaser\ParametrizarBundle\Entity\Empresa $empresa)
    {
        $this->empresa = $empresa;
    }    
    
    /**
     * Get Empresa
     *
     * @return dlaser\ParametrizarBundle\Entity\Empresa
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }    
    
    
    public function __toString()
    {
        return $this->getNombre();
    }

}