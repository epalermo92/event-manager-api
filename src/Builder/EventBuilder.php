<?php

namespace AppBundle\Builder;

use AppBundle\Entity\Event;

class EventBuilder
{
    public static function build($place, $date, $name, $numMaxParticipants, $description, $organizer, $participants)
    {
        if ($numMaxParticipants < 0) {
            throw new \LogicException('cant build event because participants number is negative!');
        }

        return new Event($place, $date, $name, $numMaxParticipants, $description, $organizer, $participants);
    }
}