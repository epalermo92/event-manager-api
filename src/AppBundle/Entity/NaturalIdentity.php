<?php

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

    public function __construct(string $name, string $surname)
    {
        $this->name = $name;
        $this->surname = $surname;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }


    public function getType(): string
    {
        return self::NATURAL;
    }
}