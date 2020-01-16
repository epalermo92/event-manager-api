<?php declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="Identity")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"natural" = "NaturalIdentity", "legal" = "LegalIdentity"})
 */
abstract class AbstractIdentity implements \JsonSerializable
{
    protected const LEGAL = 'legal';

    protected const NATURAL = 'natural';

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    /**
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

    public function addEventParticipant(Event $event): void
    {
        $this->events[] = $event;
    }

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
