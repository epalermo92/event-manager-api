<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class NaturalIdentity extends AbstractIdentity
{
    public function __construct($name, $surname)
    {
        parent::__construct($name, $surname, 'natural');
    }


    public function getType(): string
    {
        return self::NATURAL;
    }
}