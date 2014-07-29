<?php

namespace dlaser\ParametrizarBundle\Entity; 

use Doctrine\ORM\Mapping as ORM;

/**
 * dlaser\ParametrizarBundle\Entity\Plantilla
 *
 * @ORM\Table(name="plantilla")
 * @ORM\Entity
 */
class Plantilla
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
     * @var text $formato
     *
     * @ORM\Column(name="formato", type="text", nullable=true)
     */
    private $formato;

    /**
     * @var integer $cargoId
     *
     * @ORM\Column(name="cargo_id", type="integer", nullable=false)
     */
    private $cargoId;

    /**
     * @var integer $usuarioId
     *
     * @ORM\Column(name="usuario_id", type="integer", nullable=false)
     */
    private $usuarioId;



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
     * Set formato
     *
     * @param text $formato
     */
    public function setFormato($formato)
    {
        $this->formato = $formato;
    }

    /**
     * Get formato
     *
     * @return text 
     */
    public function getFormato()
    {
        return $this->formato;
    }

    /**
     * Set cargoId
     *
     * @param integer $cargoId
     */
    public function setCargoId($cargoId)
    {
        $this->cargoId = $cargoId;
    }

    /**
     * Get cargoId
     *
     * @return integer 
     */
    public function getCargoId()
    {
        return $this->cargoId;
    }

    /**
     * Set usuarioId
     *
     * @param integer $usuarioId
     */
    public function setUsuarioId($usuarioId)
    {
        $this->usuarioId = $usuarioId;
    }

    /**
     * Get usuarioId
     *
     * @return integer 
     */
    public function getUsuarioId()
    {
        return $this->usuarioId;
    }
}