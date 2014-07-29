<?php

namespace dlaser\AgendaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * dlaser\AgendaBundle\Entity\Restriccion
 *
 * @ORM\Table(name="restriccion")
 * @ORM\Entity
 */
class Restriccion
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
     * @var integer $cantidad
     *
     * @ORM\Column(name="cantidad", type="integer", nullable=true)
     * @Assert\Min(limit = "1", message = "El valor ingresado no puede ser menor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un número válido")
     */
    private $cantidad;

    /**
     * @var Cargo
     *
     * @ORM\ManyToOne(targetEntity="dlaser\ParametrizarBundle\Entity\Cargo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cargo_id", referencedColumnName="id")
     * })
     */
    private $cargo;

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
     * @var Cliente
     *
     * @ORM\ManyToOne(targetEntity="dlaser\ParametrizarBundle\Entity\Cliente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     * })
     */
    private $cliente;
    
    /**
     * @var Agenda
     *
     * @ORM\ManyToOne(targetEntity="dlaser\AgendaBundle\Entity\Agenda", inversedBy="restricciones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agenda_id", referencedColumnName="id")
     * })
     */
    private $agenda;   
    
    /**
     * @Assert\True(message = "La restricción es incorrecta defina algún parametro.")
     */
    public function isRestriccionLegal()
    {
        if ($this->cantidad || $this->cargo || $this->cliente || $this->sede){
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
     * Set cantidad
     *
     * @param integer $cantidad
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    /**
     * Get cantidad
     *
     * @return integer 
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set cargo
     *
     * @param dlaser\ParametrizarBundle\Entity\Cargo $cargo
     */
    public function setCargo(\dlaser\ParametrizarBundle\Entity\Cargo $cargo)
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
}