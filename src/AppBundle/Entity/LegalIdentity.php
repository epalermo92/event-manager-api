<?php declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class LegalIdentity extends AbstractIdentity
{
    /**
     * @ORM\Column(name="partitaIva", type="string")
     */
    private $partitaIva;

    public function __construct(string $name, string $partitaIva)
    {
        parent::__construct();

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
            'id' => $this->id ,
            'name' => $this->name ,
            'partitaIva' => $this->partitaIva ,
        ];
    }

    public function updateIdentity(LegalIdentity $newIdentity):void
    {
        $this->name = $newIdentity->getName();
        $this->partitaIva = $newIdentity->getPartitaIva();
    }
}

