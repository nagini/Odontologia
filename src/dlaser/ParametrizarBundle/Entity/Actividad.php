<?php

namespace dlaser\ParametrizarBundle\Entity; 

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * dlaser\ParametrizarBundle\Entity\Actividad
 * 
 * @ORM\Table(name="actividad")
 * @ORM\Entity
 */
class Actividad
{
     /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="dlaser\ParametrizarBundle\Entity\Cargo")
     */
    private $cargo;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="dlaser\ParametrizarBundle\Entity\Contrato")
     */
    private $contrato;   
    
    /**
     * @var integer $precio
     * 
     * @ORM\Column(name="precio", type="integer", nullable=false)
     * @Assert\Min(limit = "99", message = "El valor ingresado no puede ser menor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un número válido")
     * @Assert\Max(limit = "9999999", message = "El valor ingresado no puede ser menor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un número válido")
     */
    private $precio;
    
    /**
     * @var string $estado
     * 
     * @ORM\Column(name="estado", type="string", nullable=false)
     * @Assert\Choice(choices = {"I", "A"}, message = "Selecciona una opción valida.")
     */
    private $estado;

    /**
     * Set precio
     *
     * @param integer $precio
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }

    /**
     * Get precio
     *
     * @return ineter 
     */
    public function getPrecio()
    {
        return $this->precio;
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
     * Set contrato
     *
     * @param dlaser\ParametrizarBundle\Entity\Contrato $contrato
     */
    public function setContrato(\dlaser\ParametrizarBundle\Entity\Contrato $contrato)
    {
        $this->contrato = $contrato;
    }

    /**
     * Get contrato
     *
     * @return dlaser\ParametrizarBundle\Entity\Contrato 
     */
    public function getContrato()
    {
        return $this->contrato;
    }
}