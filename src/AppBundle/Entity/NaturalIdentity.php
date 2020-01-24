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

    public function __construct(string $name, string $surname, string $codiceFiscale)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->codiceFiscale = $codiceFiscale;
    }

    public function getType(): string
    {
        return self::NATURAL;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'type' => self::NATURAL,
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'codiceFiscale' => $this->codiceFiscale,
        ];
    }

    /**
     * @param NaturalIdentity $newIdentity
     *
     * @return AbstractIdentity
     */
    public function updateIdentity(AbstractIdentity $newIdentity): AbstractIdentity
    {
        $this->name = $newIdentity->getName();
        $this->surname = $newIdentity->getSurname();
        $this->codiceFiscale = $newIdentity->getCodiceFiscale();

        return $this;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getCodiceFiscale(): string
    {
        return $this->codiceFiscale;
    }
}
