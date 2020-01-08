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
        $manager->persist(new NaturalIdentity('Cristiano', 'Ronaldo'));
        $manager->persist(new NaturalIdentity('Lionel', 'Messi'));
        $manager->persist(new NaturalIdentity('Ciro', 'Immobile'));
        $manager->persist(new NaturalIdentity('Gianluigi', 'Buffon'));
        $manager->persist(new LegalIdentity('Juventus'));
        $manager->persist(new LegalIdentity('Real Madrid'));
        $manager->persist(new LegalIdentity('Barilla'));
        $manager->persist(new LegalIdentity('Lindt'));

        $manager->flush();
    }
}