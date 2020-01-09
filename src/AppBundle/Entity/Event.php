<?php declare(strict_types=1);

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\AbstractType;

/**
 * @ORM\Entity()
 * @ORM\Table(name="Event")
 */
class Event implements \JsonSerializable
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
     * @ORM\Column(name="num_max_participants", type="integer")
     */
    private $numMaxParticipants;

    /**
     * @var string
     * @ORM\Column(name="description", type="string")
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="AbstractIdentity",cascade={"persist"})
     */
    private $organizer;

    /**
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

    /**
     * @return string
     */
    public function getPlace(): string
    {
        return $this->place;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
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
     * @return AbstractIdentity
     */
    public function getOrganizer(): AbstractIdentity
    {
        return $this->organizer;
    }

    /**
     * @return ArrayCollection
     */
    public function getParticipants(): ArrayCollection
    {
        return $this->participants;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'description' => $this->description,
            'id' => $this->id,
            'participants'=> $this->participants
        ];
    }
}
