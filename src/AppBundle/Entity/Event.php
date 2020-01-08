<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="Event")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="event")
 */
class Event
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
     * @ORM\Column(name="place", type="string")
     */
    private $place;

    /**
     * @var DateTime
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="num_max_participants", type="integer")
     */
    private $numMaxParticipants;

    /**
     * @var string
     * @ORM\Column(name="description", type="string")
     */
    private $description;

    /**
     * @var NaturalIdentity|LegalIdentity
     * @ORM\OneToOne(targetEntity="AbstractIdentity")
     * @ORM\JoinColumn(name="organizer",referencedColumnName="id")
     */
    private $organizer;

    /**
     * @var NaturalIdentity[]
     * @ORM\Column(name="participants", type="string")
     */
    private $participants;

    public function __construct($place, $date, $name, $numMaxParticipants, $description, $organizer, $participants)
    {
        $this->place = $place;
        $this->date = $date;
        $this->name = $name;
        $this->numMaxParticipants = $numMaxParticipants;
        $this->description = $description;
        $this->organizer = $organizer;
        $this->participants = $participants;
    }

    /**
     * @return string
     */
    public function getPlace(): string
    {
        return $this->place;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getNumMaxParticipants(): int
    {
        return $this->numMaxParticipants;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return LegalIdentity|NaturalIdentity
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }

    /**
     * @return NaturalIdentity[]
     */
    public function getParticipants(): array
    {
        return $this->participants;
    }
}