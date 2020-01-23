<?php declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="Identity")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"natural" = "NaturalIdentity", "legal" = "LegalIdentity"})
 */
abstract class AbstractIdentity implements \JsonSerializable
{
    public const LEGAL = 'legal';

    public const NATURAL = 'natural';

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract public function getType(): string;

    abstract public function updateIdentity(AbstractIdentity $newIdentity): AbstractIdentity;
}
