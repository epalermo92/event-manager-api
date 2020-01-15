<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\AbstractIdentity;
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
        $organizer = $manager->getRepository(AbstractIdentity::class)->find(2);
        $manager->persist(new Event('Santiago Bernabeu', new DateTime(), 'Finale Champions League', 100000, 'description', $organizer, $organizer));
        $organizer = $manager->getRepository(AbstractIdentity::class)->find(7);
        $manager->persist(new Event('Santiago Bernabeu', new DateTime(), 'Finale Champions League', 100000, 'description', $organizer, $organizer));

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return array(
            LoadIdentity::class,
        );
    }
}
