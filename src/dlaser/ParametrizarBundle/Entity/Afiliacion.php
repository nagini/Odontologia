<?php

namespace dlaser\ParametrizarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * dlaser\ParametrizarBundle\Entity\Afiliacion
 * 
 * @ORM\Table(name="afiliacion")
 * @ORM\Entity
 */
class Afiliacion
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="dlaser\ParametrizarBundle\Entity\Paciente")
     */
    private $paciente; 
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="dlaser\ParametrizarBundle\Entity\Cliente")
     */
    private $cliente;
    
    
          
    /**
     * @var string $observacion
     * 
     * @ORM\Column(name="observacion", type="string", length=255, nullable=true)
     * @Assert\MaxLength(limit=255, message="El valor ingresado debe tener m��ximo {{ limit }} caracteres.")
     */
    private $observacion;

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
     * Set paciente
     *
     * @param dlaser\ParametrizarBundle\Entity\Paciente $paciente
     */
    public function setPaciente(\dlaser\ParametrizarBundle\Entity\Paciente $paciente)
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
}