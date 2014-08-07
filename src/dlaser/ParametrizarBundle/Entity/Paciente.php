<?php

namespace dlaser\ParametrizarBundle\Entity; 

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;


/**
 * @ORM\Entity(repositoryClass="dlaser\ParametrizarBundle\Entity\Repository\PacienteRepository")
 * dlaser\ParametrizarBundle\Entity\Paciente
 *
 * @ORM\Table(name="paciente")
 * @ORM\Entity
 * @DoctrineAssert\UniqueEntity("identificacion")
 */
class Paciente
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
     * @var string $tipoId
     * 
     * @ORM\Column(name="tipo_id", type="string", length=2, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Choice(choices = {"CC", "RC", "TI", "CE"}, message = "Selecciona una opción valida.")
     */
    private $tipoId;

    /**
     * @var string $identificacion
     * 
     * @ORM\Column(name="identificacion", type="string", length=13, unique=true)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Min(limit = "10000", message = "El valor ingresado no puede ser menor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un n��mero v��lido")
     * @Assert\Max(limit = "9999999999999", message = "El valor ingresado no puede ser mayor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un n��mero v��lido")
     */
    private $identificacion;

    /**
     * @var string $priNombre
     * 
     * @ORM\Column(name="pri_nombre", type="string", length=30, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\MaxLength(limit=30, message="El valor ingresado debe tener máximo {{ limit }} caracteres.")
     */
    private $priNombre;

    /**
     * @var string $segNombre
     * 
     * @ORM\Column(name="seg_nombre", type="string", length=30, nullable=true)
     * @Assert\MaxLength(limit=30, message="El valor ingresado debe tener máximo {{ limit }} caracteres.")
     */
    private $segNombre;

    /**
     * @var string $priApellido
     * 
     * @ORM\Column(name="pri_apellido", type="string", length=30, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\MaxLength(limit=30, message="El valor ingresado debe tener máximo {{ limit }} caracteres.")
     */
    private $priApellido;

    /**
     * @var string $segApellido
     *
     * @ORM\Column(name="seg_apellido", type="string", length=30, nullable=true)
     * @Assert\MaxLength(limit=30, message="El valor ingresado debe tener máximo {{ limit }} caracteres.")
     */
    private $segApellido;

    /**
     * @var datetime $fN
     * 
     * @ORM\Column(name="f_n", type="datetime", nullable=false)
     * @Assert\DateTime()
     */
    private $fN;

    /**
     * @var string $sexo
     * 
     * @ORM\Column(name="sexo", type="string", length=1, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Choice(choices = {"M", "F"}, message = "Selecciona una opción valida.")
     */
    private $sexo;

    /**
     * @var integer $mupio
     * 
     * @ORM\Column(name="depto", type="integer", nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * 
     */
    private $depto;

    /**
     * @var integer $mupio
     * 
     * @ORM\Column(name="mupio", type="integer", nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     */
    private $mupio;

    /**
     * @var string $direccion
     *
     * @ORM\Column(name="direccion", type="string", length=60, nullable=true)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\MaxLength(limit=60, message="El valor ingresado debe tener m��ximo {{ limit }} caracteres.")
     */
    private $direccion;

    /**
     * @var string $zona
     *
     * @ORM\Column(name="zona", type="string", length=1, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Choice(choices = {"U", "R"}, message = "Selecciona una opci��n valida.")
     */
    private $zona;

    /**
     * @var string $telefono      
     * 
     * @ORM\Column(name="telefono", type="string", length=7, nullable=true)
	 * @Assert\Min(limit = "1000000", message = "El valor ingresado no puede ser menor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un n��mero v��lido")
	 * @Assert\Max(limit = "9999999", message = "El valor ingresado no puede ser mayor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un n��mero v��lido")
     */
    private $telefono;

    /**
     * @var string $movil
     * 
     * @ORM\Column(name="movil", type="string", length=10, nullable=true)
     * @Assert\Min(limit = "3000000000", message = "El valor ingresado no puede ser menor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un n��mero v��lido")
	 * @Assert\Max(limit = "9999999999", message = "El valor ingresado no puede ser mayor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un n��mero v��lido")
     */
    private $movil;

    /**
     * @var string $email
     * 
     * @ORM\Column(name="email", type="string", length=200, nullable=true)
     * @Assert\Email(message = "El email '{{ value }}' no es valido.", checkMX = true)
     */
    private $email;

        
    
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
     * Set tipoId
     *
     * @param string $tipoId
     */
    public function setTipoId($tipoId)
    {
        $this->tipoId = $tipoId;
    }

    /**
     * Get tipoId
     *
     * @return string 
     */
    public function getTipoId()
    {
        return $this->tipoId;
    }

    /**
     * Set identificacion
     *
     * @param string $identificacion
     */
    public function setIdentificacion($identificacion)
    {
        $this->identificacion = $identificacion;
    }

    /**
     * Get identificacion
     *
     * @return string 
     */
    public function getIdentificacion()
    {
        return $this->identificacion;
    }

    /**
     * Set priNombre
     *
     * @param string $priNombre
     */
    public function setPriNombre($priNombre)
    {
        $this->priNombre = $priNombre;
    }

    /**
     * Get priNombre
     *
     * @return string 
     */
    public function getPriNombre()
    {
        return $this->priNombre;
    }

    /**
     * Set segNombre
     *
     * @param string $segNombre
     */
    public function setSegNombre($segNombre)
    {
        $this->segNombre = $segNombre;
    }

    /**
     * Get segNombre
     *
     * @return string 
     */
    public function getSegNombre()
    {
        return $this->segNombre;
    }

    /**
     * Set priApellido
     *
     * @param string $priApellido
     */
    public function setPriApellido($priApellido)
    {
        $this->priApellido = $priApellido;
    }

    /**
     * Get priApellido
     *
     * @return string 
     */
    public function getPriApellido()
    {
        return $this->priApellido;
    }

    /**
     * Set segApellido
     *
     * @param string $segApellido
     */
    public function setSegApellido($segApellido)
    {
        $this->segApellido = $segApellido;
    }

    /**
     * Get segApellido
     *
     * @return string 
     */
    public function getSegApellido()
    {
        return $this->segApellido;
    }

    /**
     * Set fN
     *
     * @param datetime $fN
     */
    public function setFN($fN)
    {
        $this->fN = $fN;
    }

    /**
     * Get fN
     *
     * @return datetime 
     */
    public function getFN()
    {
        return $this->fN;
    }

    /**
     * Set sexo
     *
     * @param string $sexo
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    }

    /**
     * Get sexo
     *
     * @return string 
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Set depto
     *
     * @param integer $depto
     */
    public function setDepto($depto)
    {
        $this->depto = $depto;
    }

    /**
     * Get depto
     *
     * @return integer 
     */
    public function getDepto()
    {
        return $this->depto;
    }

    /**
     * Set mupio
     *
     * @param integer $mupio
     */
    public function setMupio($mupio)
    {
        $this->mupio = $mupio;
    }

    /**
     * Get mupio
     *
     * @return integer 
     */
    public function getMupio()
    {
        return $this->mupio;
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
     * Set zona
     *
     * @param string $zona
     */
    public function setZona($zona)
    {
        $this->zona = $zona;
    }

    /**
     * Get zona
     *
     * @return string 
     */
    public function getZona()
    {
        return $this->zona;
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
    
}