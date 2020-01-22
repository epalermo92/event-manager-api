<?php declare(strict_types=1);

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="Event")
 */
class Event implements \JsonSerializable
{
    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="place", type="string")
     */
    private $place;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_max_participants", type="integer")
     */
    private $numMaxParticipants;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string")
     */
    private $description;

    /**
     * @var AbstractIdentity
     *
     * @ORM\OneToOne(targetEntity="AbstractIdentity",cascade={"persist"})
     */
    private $organizer;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="NaturalIdentity",mappedBy="events")
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
        $this->participants = new ArrayCollection();
    }

    public function addParticipant(NaturalIdentity $participant): void
    {
        $this->participants[] = $participant;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlace(): string
    {
        return $this->place;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNumMaxParticipants(): int
    {
        return $this->numMaxParticipants;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getOrganizer(): AbstractIdentity
    {
        return $this->organizer;
    }

    public function getParticipants(): ArrayCollection
    {
        return $this->participants;
    }

    public function updateEntity(Event $newEvent): self
    {
        $this->place = $newEvent->getPlace();
        $this->name = $newEvent->getName();
        $this->description = $newEvent->getDescription();
        $this->numMaxParticipants = $newEvent->getNumMaxParticipants();
        $this->organizer = $newEvent->getOrganizer();
//        $this->participants = $newEvent->getParticipants();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'num_max_participants' => $this->numMaxParticipants,
            'participants' => $this->participants->toArray(),
            'place' => $this->place,
            'date' => $this->date,
            'organizer' => $this->organizer
        ];
    }
}
