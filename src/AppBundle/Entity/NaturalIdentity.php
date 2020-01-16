<?php declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class NaturalIdentity extends AbstractIdentity
{
    /**
     * @var string
     * @ORM\Column(name="surname", type="string")
     */
    private $surname;

    /**
     * @ORM\Column(name="codiceFiscale", type="string")
     */
    private $codiceFiscale;
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Event",inversedBy="participants")
     */
    protected $events;

    public function __construct(string $name, string $surname, string $codiceFiscale)
    {
        parent::__construct();

        $this->name = $name;
        $this->surname = $surname;
        $this->codiceFiscale = $codiceFiscale;
        $this->events = new ArrayCollection();
    }

    public function getSurname(): string
    {
        return $this->surname;
    }


    public function getType(): string
    {
        return self::NATURAL;
    }

    public function getCodiceFiscale():string
    {
        return $this->codiceFiscale;
    }

    /**
     * @return ArrayCollection
     */
    public function getEvents(): ArrayCollection
    {
        return $this->events;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'codiceFiscale' => $this->codiceFiscale,
            'events' => $this->events
        ];
    }

    public function updateIdentity(NaturalIdentity $newIdentity) : self
    {
        $this->name = $newIdentity->getName();
        $this->surname = $newIdentity->getSurname();
        $this->codiceFiscale = $newIdentity->getCodiceFiscale();

        return $this;
    }
}
