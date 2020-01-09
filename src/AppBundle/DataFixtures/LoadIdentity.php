<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\LegalIdentity;
use AppBundle\Entity\NaturalIdentity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadIdentity extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $manager->persist(new NaturalIdentity('Cristiano', 'Ronaldo', 'VVVLTK81D18I723B'));
        $manager->persist(new NaturalIdentity('Lionel', 'Messi','XKMDRH81M66H101H'));
        $manager->persist(new NaturalIdentity('Ciro', 'Immobile','FFGJRF47B24A681B'));
        $manager->persist(new NaturalIdentity('Gianluigi', 'Buffon','MNNNPV49A58E932I'));
        $manager->persist(new LegalIdentity('Juventus','12345678912'));
        $manager->persist(new LegalIdentity('Real Madrid','78945612378'));
        $manager->persist(new LegalIdentity('Barilla','98765432198'));
        $manager->persist(new LegalIdentity('Lindt','32165498732'));

        $manager->flush();
    }
}
