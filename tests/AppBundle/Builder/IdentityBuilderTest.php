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

        $this->assertSame(get_class($legalIdentity), Right::class);
        $this->assertSame(get_class($legalIdentity->extract()), NaturalIdentity::class);
    }

    public function testBuildNatural(): void
    {
        $legalIdentity = IdentityBuilder::build([
            'name' => 'Facile.it',
            'partitaIva' => '2395678923',
            'type' => 'legal',
        ]);

        $this->assertSame(get_class($legalIdentity), Right::class);
        $this->assertSame(get_class($legalIdentity->extract()), LegalIdentity::class);
    }

    public function testWrongData(): void
    {
        $legalIdentity = IdentityBuilder::build([
            'name' => 'Pippo',
            'surname' => 'Pluto',
            'codiceFiscale' => 'PPOPLT23R19D245G',
            'type' => 'naturale',
        ]);

        $this->assertSame(get_class($legalIdentity), Left::class);
    }
}
