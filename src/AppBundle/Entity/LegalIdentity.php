<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class LegalIdentity extends AbstractIdentity
{
    public function __construct($name, $surname)
    {
        parent::__construct($name, $surname, 'legal');
    }

    public function getType(): string
    {
        return self::LEGAL;
    }
}