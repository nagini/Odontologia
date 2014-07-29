<?php

namespace dlaser\ParametrizarBundle\Entity; 

use Doctrine\ORM\Mapping as ORM;

/**
 * dlaser\ParametrizarBundle\Entity\Mupio
 *
 * @ORM\Table(name="mupio")
 * @ORM\Entity
 */
class Mupio
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
     * @var integer $codigo
     * 
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     */
    private $codigo;

    /**
     * @var string $municipio
     * 
     * @ORM\Column(name="municipio", type="string", length=90, nullable=false)
     */
    private $municipio;

    /**
     * @var Depto
     *
     * @ORM\ManyToOne(targetEntity="Depto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="depto_id", referencedColumnName="id")
     * })
     */
    private $depto;
    
    
    public function __toString()
    {
        return $this->getMunicipio();
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
     * Set codigo
     *
     * @param integer $codigo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    /**
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set municipio
     *
     * @param string $municipio
     */
    public function setMunicipio($municipio)
    {
        $this->municipio = $municipio;
    }

    /**
     * Get municipio
     *
     * @return string 
     */
    public function getMunicipio()
    {
        return $this->municipio;
    }

    /**
     * Set depto
     *
     * @param dlaser\ParametrizarBundle\Entity\Depto $depto
     */
    public function setDepto(\dlaser\ParametrizarBundle\Entity\Depto $depto)
    {
        $this->depto = $depto;
    }

    /**
     * Get depto
     *
     * @return dlaser\ParametrizarBundle\Entity\Depto 
     */
    public function getDepto()
    {
        return $this->depto;
    }
}