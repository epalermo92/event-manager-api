<?php

namespace AppBundle\Entity;

class Event
{
    /** @var string $place */
    private $place;

    /** @var \DateTime $date */
    private $date;

    /** @var string $name */
    private $name;

    /** @var int $numMaxParticipants */
    private $numMaxParticipants;

    /** @var string $description */
    private $description;

    /** @var \LegalIdentity|\NaturalIdentity $organizer */
    private $organizer;

    /** @var \NaturalIdentity[] $participants */
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
     * @return \DateTime
     */
    public function getDate(): \DateTime
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
     * @return \LegalIdentity|\NaturalIdentity
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }

    /**
     * @return \NaturalIdentity[]
     */
    public function getParticipants(): array
    {
        return $this->participants;
    }
}