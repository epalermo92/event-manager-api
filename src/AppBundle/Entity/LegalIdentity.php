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
        $this->name = $name;
        $this->partitaIva = $partitaIva;
    }

    public function getType(): string
    {
        return self::LEGAL;
    }

    public function getPartitaIva()
    {
        return $this->partitaIva;
    }
}

