<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Event;
use AppBundle\Entity\LegalIdentity;
use AppBundle\Entity\NaturalIdentity;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadEvent extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist(new Event('Santiago Bernabeu', new DateTime(), 'Finale Champions League', 100000, 'description', new NaturalIdentity('Cristiano','Ronaldo'), 7));
        $manager->persist(new Event('Santiago Bernabeu', new DateTime(), 'Finale Champions League', 100000, 'description', new NaturalIdentity('Cristiano','Ronaldo'), 8));

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return array(
            LoadIdentity::class,
        );
    }
}