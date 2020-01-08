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
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="Event",inversedBy="participants")
     * @ORM\JoinTable(name="event_participants")
     */
    protected $events;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract public function getType(): string;
}