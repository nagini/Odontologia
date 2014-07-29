<?php

namespace dlaser\ParametrizarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * dlaser\ParametrizarBundle\Entity\Contrato
 * 
 * @ORM\Table(name="contrato")
 * @ORM\Entity
 */
class Contrato
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
     * @var string $contacto
     * 
     * @ORM\Column(name="contacto", type="string", length=80, nullable=true)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\MaxLength(limit=80, message="El valor ingresado debe tener maximo {{ limit }} caracteres.")
     */
    private $contacto;
    
    /**
     * @var string $cargo
     *
     * @ORM\Column(name="cargo", type="string", length=30, nullable=true)
     * @Assert\MaxLength(limit=30, message="El valor ingresado debe tener maximo {{ limit }} caracteres.")
     */
    private $cargo;
    
    /**
     * @var integer $telefono
     * 
     * @ORM\Column(name="telefono", type="integer", nullable=true)
     * @Assert\Min(limit = "1000000", message = "El valor ingresado no puede ser menor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un n��mero v��lido")
     * @Assert\Max(limit = "9999999", message = "El valor ingresado no puede ser mayor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un n��mero v��lido")
     */
    private $telefono;

    /**
     * @var string $celular
     * 
     * @ORM\Column(name="celular", type="string", length=10, nullable=true)
     * @Assert\Min(limit = "3000000000", message = "El valor ingresado no puede ser menor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un n��mero v��lido")
     * @Assert\Max(limit = "9999999999", message = "El valor ingresado no puede ser mayor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un n��mero v��lido")
     */
    private $celular;
    
    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=200, nullable=true)
     * @Assert\Email(message = "El email '{{ value }}' no es valido.", checkMX = true)
     */
    private $email;
    
    /**
     * @var string $estado
     *
     * @ORM\Column(name="estado", type="string", length=1, nullable=true)
     * @Assert\Choice(choices = {"I", "A"}, message = "Selecciona una opción valida.")
     */
    private $estado;
    
    /**
     * @var integer $porcentaje
     *
     * @ORM\Column(name="porcentaje", type="decimal", scale=2, nullable=true)
     * @Assert\Min(limit = "-50", message = "El valor ingresado no puede ser menor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un número válido")
     * @Assert\Max(limit = "100", message = "El valor ingresado no puede ser mayor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un número válido")
     */
    private $porcentaje;
    
    /**
     * @var string $observacion
     *
     * @ORM\Column(name="observacion", type="string", length=200, nullable=true)
     * @Assert\MaxLength(limit=200, message="El valor ingresado debe tener máximo {{ limit }} caracteres.")
     */
    private $observacion;
    
    /**
     * @var Cliente
     *
     * @ORM\ManyToOne(targetEntity="dlaser\ParametrizarBundle\Entity\Cliente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     * })
     */
    private $cliente;
    
    /**
     * @var Sede
     *
     * @ORM\ManyToOne(targetEntity="dlaser\ParametrizarBundle\Entity\Sede")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sede_id", referencedColumnName="id")
     * })
     */
    private $sede;

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
     * Set contacto
     *
     * @param string $contacto
     */
    public function setContacto($contacto)
    {
        $this->contacto = $contacto;
    }

    /**
     * Get contacto
     *
     * @return string 
     */
    public function getContacto()
    {
        return $this->contacto;
    }

    /**
     * Set cargo
     *
     * @param string $cargo
     */
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;
    }

    /**
     * Get cargo
     *
     * @return string 
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * Set telefono
     *
     * @param integer $telefono
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    /**
     * Get telefono
     *
     * @return integer 
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set celular
     *
     * @param string $celular
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;
    }

    /**
     * Get celular
     *
     * @return string 
     */
    public function getCelular()
    {
        return $this->celular;
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
     * Set estado
     *
     * @param string $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * Get estado
     *
     * @return string 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set porcentaje
     *
     * @param integer $porcentaje
     */
    public function setPorcentaje($porcentaje)
    {
        $this->porcentaje = $porcentaje;
    }

    /**
     * Get porcentaje
     *
     * @return integer 
     */
    public function getPorcentaje()
    {
        return $this->porcentaje;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set cliente
     *
     * @param dlaser\ParametrizarBundle\Entity\Cliente $cliente
     */
    public function setCliente(\dlaser\ParametrizarBundle\Entity\Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    /**
     * Get cliente
     *
     * @return dlaser\ParametrizarBundle\Entity\Cliente 
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set sede
     *
     * @param dlaser\ParametrizarBundle\Entity\Sede $sede
     */
    public function setSede(\dlaser\ParametrizarBundle\Entity\Sede $sede)
    {
        $this->sede = $sede;
    }

    /**
     * Get sede
     *
     * @return dlaser\ParametrizarBundle\Entity\Sede 
     */
    public function getSede()
    {
        return $this->sede;
    }
}