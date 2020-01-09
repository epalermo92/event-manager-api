<?php declare(strict_types=1);

namespace AppBundle\Entity;

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
}
