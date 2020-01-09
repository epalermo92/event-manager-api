<?php declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class LegalIdentity extends AbstractIdentity
{
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getType(): string
    {
        return self::LEGAL;
    }
}
