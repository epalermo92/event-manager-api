<?php


namespace Tests\AppBundle\Builder;


use AppBundle\Builder\IdentityBuilder;
use AppBundle\Entity\LegalIdentity;
use AppBundle\Entity\NaturalIdentity;
use PHPUnit\Framework\TestCase;
use Widmogrod\Monad\Either\Left;
use Widmogrod\Monad\Either\Right;

class IdentityBuilderTest extends TestCase
{
    public function testBuildLegal(): void
    {
        $legalIdentity = IdentityBuilder::build([
            'name' => 'Pippo',
            'surname' => 'Pluto',
            'codiceFiscale' => 'PPOPLT23R19D245G',
            'type' => 'natural',
        ]);

        $this->assertSame(Right::class, get_class($legalIdentity));
        $this->assertSame(NaturalIdentity::class, get_class($legalIdentity->extract()));
    }

    public function testBuildNatural(): void
    {
        $legalIdentity = IdentityBuilder::build([
            'name' => 'Facile.it',
            'partitaIva' => '2395678923',
            'type' => 'legal',
        ]);

        $this->assertSame(Right::class, get_class($legalIdentity));
        $this->assertSame(LegalIdentity::class, get_class($legalIdentity->extract()));
    }

    public function testWrongData(): void
    {
        $legalIdentity = IdentityBuilder::build([
            'name' => 'Pippo',
            'surname' => 'Pluto',
            'codiceFiscale' => 'PPOPLT23R19D245G',
            'type' => 'naturale',
        ]);

        $this->assertSame(Left::class, get_class($legalIdentity));
    }
}
