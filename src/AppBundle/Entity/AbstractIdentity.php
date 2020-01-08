<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="Identity")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"natural" = "NaturalIdentity", "legal" = "LegalIdentity"})
 */
abstract class AbstractIdentity
{
    protected const LEGAL = 'legal';

    protected const NATURAL = 'natural';

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

    abstract public function getType(): string;
}