<?php declare(strict_types=1);

namespace Tests\AppBundle\Builder;

use AppBundle\Builder\EventBuilder;
use AppBundle\Entity\Event;
use PHPUnit\Framework\TestCase;
use Widmogrod\Monad\Either\Left;
use Widmogrod\Monad\Either\Right;

class EventBuilderTest extends TestCase
{
    public function testEventBuildRight(): void
    {
        $event = EventBuilder::build(
            [
                'name' => 'Christmas Party',
                'description' => 'Festa di Natale',
                'place' => 'Burigozzo 1',
                'num_max_participants' => 300,
                'organizer' => [1],
                'participants' => [2],
                'date' => new \DateTime()
            ]
        );

        $this->assertSame(Right::class,get_class($event));
        $this->assertSame(Event::class,get_class($event->extract()));
    }

    public function testEventBuildLeft(): void
    {
        $event = EventBuilder::build(
            [
                'name' => 'Christmas Party',
                'description' => 'Festa di Natale',
                'place' => 'Burigozzo 1',
                'num_max_participants' => -1,
                'organizer' => [1],
                'participants' => [2],
                'date' => new \DateTime()
            ]
        );

        $this->assertSame(Left::class,get_class($event));
        $this->assertSame(\LogicException::class,get_class($event->extract()));
    }
}
