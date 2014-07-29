<?php

namespace dlaser\ParametrizarBundle\Entity; 

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * dlaser\ParametrizarBundle\Entity\Empresa
 *
 * @ORM\Table(name="empresa")
 * @ORM\Entity
 * @DoctrineAssert\UniqueEntity(fields = "nit", message = "El valor ingresado ya esta siendo usado en el sistema.")
 */
class Empresa
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
     * @var string $nit
     * 
     * @ORM\Column(name="nit", type="string", length=12, nullable=false, unique=true)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Min(limit = "1000000", message = "El valor ingresado no puede ser menor de {{ limit }}", invalidMessage = "El valor ingresado debe ser un n��mero v��lido")
     */
    private $nit;

    /**
     * @var string $nombre
     * 
     * @ORM\Column(name="nombre", type="string", length=80, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\MinLength(limit=3, message="El valor ingresado debe tener al menos {{ limit }} caracteres.")
     */
    private $nombre;



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
     * Set nit
     *
     * @param string $nit
     */
    public function setNit($nit)
    {
        $this->nit = $nit;
    }

    /**
     * Get nit
     *
     * @return string 
     */
    public function getNit()
    {
        return $this->nit;
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
}