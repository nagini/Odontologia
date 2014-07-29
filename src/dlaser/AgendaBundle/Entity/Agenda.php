<?php

namespace dlaser\AgendaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * dlaser\AgendaBundle\Entity\Agenda
 *
 * @ORM\Table(name="agenda")
 * @ORM\Entity
 */
class Agenda
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
     * @var datetime $fechaInicio
     *
     * @ORM\Column(name="fecha_inicio", type="datetime", nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\DateTime(message = "Estos valores no son una fecha y hora válida")
     */
    private $fechaInicio;

    /**
     * @var datetime $fechaFin
     *
     * @ORM\Column(name="fecha_fin", type="datetime", nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\DateTime(message = "Estos valores no son una fecha y hora válida")
     */
    private $fechaFin;

    /**
     * @var integer $intervalo
     *
     * @ORM\Column(name="intervalo", type="integer", nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Min(limit = "1", message = "El valor ingresado no puede ser menor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un número válido")
     * @Assert\Max(limit = "60", message = "El valor ingresado no puede ser menor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un número válido")
     */
    private $intervalo;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="estado", type="string", length=2, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Choice(choices = {"I", "A"}, message = "Selecciona una opción valida.")
     */
    private $estado;

    /**
     * @var string $nota
     *
     * @ORM\Column(name="nota", type="string", length=255, nullable=true)
     * @Assert\MaxLength(limit=255, message="El valor ingresado debe tener máximo {{ limit }} caracteres.")
     */
    private $nota;

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
     * @var Usuario
     * 
     * @ORM\ManyToOne(targetEntity="dlaser\UsuarioBundle\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;
    
    /**
     * @var Restricciones
     * 
     * @ORM\OneToMany(targetEntity="Restriccion", mappedBy="Agenda")
     */
    private $restricciones;
    

    public function __construct()
    {
        $this->restricciones = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * @Assert\True(message = "La fecha de finalización debe ser mayor que la fecha de inicio de la agenda.")
     */
    public function isFechaFinLegal()
    {
        if ($this->fechaFin > $this->fechaInicio){
            return true;
        }else {
            return false;
        }
    }
    
    /**
     * @Assert\True(message = "La fecha de inicio debe ser mayor que la fecha actual.")
     */
    public function isFechaInicioLegal()
    {
        if ($this->fechaInicio > new \DateTime('now')){
            return true;
        }else {
            return false;
        }
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
     * Set fechaInicio
     *
     * @param datetime $fechaInicio
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    }

    /**
     * Get fechaInicio
     *
     * @return datetime 
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin
     *
     * @param datetime $fechaFin
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;
    }

    /**
     * Get fechaFin
     *
     * @return datetime 
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * Set intervalo
     *
     * @param integer $intervalo
     */
    public function setIntervalo($intervalo)
    {
        $this->intervalo = $intervalo;
    }

    /**
     * Get intervalo
     *
     * @return integer 
     */
    public function getIntervalo()
    {
        return $this->intervalo;
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
     * Set nota
     *
     * @param string $nota
     */
    public function setNota($nota)
    {
        $this->nota = $nota;
    }

    /**
     * Get nota
     *
     * @return string 
     */
    public function getNota()
    {
        return $this->nota;
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

    /**
     * Set usuario
     *
     * @param dlaser\UsuarioBundle\Entity\Usuario $usuario
     */
    public function setUsuario(\dlaser\UsuarioBundle\Entity\Usuario $usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * Get usuario
     *
     * @return dlaser\UsuarioBundle\Entity\Usuario 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Add restricciones
     *
     * @param dlaser\AgendaBundle\Entity\Restriccion $restricciones
     */
    public function addRestriccion(\dlaser\AgendaBundle\Entity\Restriccion $restricciones)
    {
        $this->restricciones[] = $restricciones;
    }

    /**
     * Get restricciones
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getRestricciones()
    {
        return $this->restricciones;
    }
}