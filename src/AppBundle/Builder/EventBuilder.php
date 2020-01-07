<?php

namespace AppBundle\Builder;

use AppBundle\Entity\Event;
use Widmogrod\Monad\Either\Either;
use Widmogrod\Monad\Either\Right;
use function Widmogrod\Monad\Either\left;

class EventBuilder
{
    public static function build($place, $date, $name, $numMaxParticipants, $description, $organizer, $participants): Either
    {
        if ($numMaxParticipants < 0) {
            return left(new \LogicException('cant build event because participants number is negative!'));
        }

        return new right(new Event($place, $date, $name, $numMaxParticipants, $description, $organizer, $participants));
    }
}