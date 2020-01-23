<?php declare(strict_types=1);

namespace Tests\AppBundle\Builder;

use AppBundle\Builder\EventBuilder;
use AppBundle\Entity\AbstractIdentity;
use AppBundle\Entity\Event;
use AppBundle\Entity\NaturalIdentity;
use PHPUnit\Framework\TestCase;
use Widmogrod\Monad\Either\Left;
use Widmogrod\Monad\Either\Right;

class EventBuilderTest extends TestCase
{
    public function testEventBuild(): void
    {
        /** @var AbstractIdentity $organizer */
        $organizer = $this->prophesize(AbstractIdentity::class)->reveal();
        /** @var NaturalIdentity $participant */
        $participant = $this->prophesize(NaturalIdentity::class)->reveal();

        $event = EventBuilder::build(
            [
                'name' => 'Christmas Party',
                'description' => 'Festa di Natale',
                'place' => 'Burigozzo 1',
                'num_max_participants' => 300,
                'organizer' => $organizer,
                'participants' => [$participant],
                'date' => new \DateTime(),
            ]
        );

        $this->assertSame(Right::class, get_class($event));
        $this->assertSame(Event::class, get_class($event->extract()));
    }
}
