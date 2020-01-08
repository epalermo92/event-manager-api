<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="Identity")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="identity")
 */
abstract class AbstractIdentity
{

    /**
     * @var integer
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="surname", type="string")
     */
    private $surname;

    /**
     * @var string
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    public function __construct($name, $surname, $type)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->type = $type;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getType(): string
    {
        return $this->type;
    }
}