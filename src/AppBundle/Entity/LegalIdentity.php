<?php declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class LegalIdentity extends AbstractIdentity
{
    /**
     * @var string
     *
     * @ORM\Column(name="partitaIva", type="string")
     */
    private $partitaIva;

    public function __construct(string $name, string $partitaIva)
    {
        $this->name = $name;
        $this->partitaIva = $partitaIva;
    }

    public function getType(): string
    {
        return self::LEGAL;
    }

    public function getPartitaIva(): string
    {
        return $this->partitaIva;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'type' => self::LEGAL,
            'id' => $this->id ,
            'name' => $this->name ,
            'partitaIva' => $this->partitaIva ,
        ];
    }

    /**
     * @param LegalIdentity $newIdentity
     *
     * @return AbstractIdentity
     */
    public function updateIdentity(AbstractIdentity $newIdentity): AbstractIdentity
    {
        $this->name = $newIdentity->getName();
        $this->partitaIva = $newIdentity->getPartitaIva();

        return $this;
    }
}

