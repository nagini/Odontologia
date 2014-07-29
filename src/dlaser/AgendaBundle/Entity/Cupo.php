<?php

namespace dlaser\AgendaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * dlaser\AgendaBundle\Entity\Cupo
 *
 * @ORM\Table(name="cupo")
 * @ORM\Entity
 */
class Cupo
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
     * @var datetime $hora
     *
     * @ORM\Column(name="hora", type="datetime", nullable=false)
     */
    private $hora;

    /**
     * @var string $estado
     *
     * @ORM\Column(name="estado", type="string", length=2, nullable=true)
     */
    private $estado;

    /**
     * @var string $nota
     *
     * @ORM\Column(name="nota", type="string", length=255, nullable=true)
     */
    private $nota;

    /**
     * @var integer $registra
     *
     * @ORM\Column(name="registra", type="integer", nullable=false)
     */
    private $registra;

    /**
     * @var string $verificacion
     *
     * @ORM\Column(name="verificacion", type="string", length=20, nullable=true)
     */
    private $verificacion;
    
    /**
     * @var integer $cliente
     *
     * @ORM\Column(name="cliente", type="integer", nullable=true)
     */
    private $cliente;

    /**
     * @var Cargo
     *
     * @ORM\ManyToOne(targetEntity="dlaser\ParametrizarBundle\Entity\Cargo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cargo_id", referencedColumnName="id", nullable="true")
     * })
     */
    private $cargo;

    /**
     * @var Agenda
     *
     * @ORM\ManyToOne(targetEntity="dlaser\AgendaBundle\Entity\Agenda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agenda_id", referencedColumnName="id")
     * })
     */
    private $agenda;

    /**
     * @var Paciente
     *
     * @ORM\ManyToOne(targetEntity="dlaser\ParametrizarBundle\Entity\Paciente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="paciente_id", referencedColumnName="id", nullable="true")
     * })
     */
    private $paciente;

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
     * Set hora
     *
     * @param datetime $hora
     */
    public function setHora($hora)
    {
        $this->hora = $hora;
    }

    /**
     * Get hora
     *
     * @return datetime 
     */
    public function getHora()
    {
        return $this->hora;
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
     * Set registra
     *
     * @param integer $registra
     */
    public function setRegistra($registra)
    {
        $this->registra = $registra;
    }

    /**
     * Get registra
     *
     * @return integer 
     */
    public function getRegistra()
    {
        return $this->registra;
    }

    /**
     * Set verificacion
     *
     * @param string $verificacion
     */
    public function setVerificacion($verificacion)
    {
        $this->verificacion = $verificacion;
    }

    /**
     * Get verificacion
     *
     * @return string 
     */
    public function getVerificacion()
    {
        return $this->verificacion;
    }
    
    /**
     * Set cliente
     *
     * @param integer $cliente
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
    }
    
    /**
     * Get cliente
     *
     * @return integer
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set cargo
     *
     * @param dlaser\ParametrizarBundle\Entity\Cargo $cargo
     */
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;
    }

    /**
     * Get cargo
     *
     * @return dlaser\ParametrizarBundle\Entity\Cargo 
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * Set agenda
     *
     * @param dlaser\AgendaBundle\Entity\Agenda $agenda
     */
    public function setAgenda(\dlaser\AgendaBundle\Entity\Agenda $agenda)
    {
        $this->agenda = $agenda;
    }

    /**
     * Get agenda
     *
     * @return dlaser\AgendaBundle\Entity\Agenda 
     */
    public function getAgenda()
    {
        return $this->agenda;
    }

    /**
     * Set paciente
     *
     * @param dlaser\ParametrizarBundle\Entity\Paciente $paciente
     */
    public function setPaciente($paciente)
    {
        $this->paciente = $paciente;
    }

    /**
     * Get paciente
     *
     * @return dlaser\ParametrizarBundle\Entity\Paciente 
     */
    public function getPaciente()
    {
        return $this->paciente;
    }
}