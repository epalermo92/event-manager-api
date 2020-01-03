<?php


class AbstractIdentity
{

    /** @var string $name */
    private $name;

    /** @var string $surname */
    private $surname;

    /** @var string $type */
    private $type;


    public function __construct( $name,  $surname,  $type)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->type = $type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSurname()
    {
        return $this->surname;
    }

     public function getType()
    {
        return $this->type;
    }

}