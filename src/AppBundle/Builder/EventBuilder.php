<?php

namespace AppBundle\Builder;

use AppBundle\Entity\Event;
use Widmogrod\Monad\Either\Either;
use Widmogrod\Monad\Either\Right;
use function Widmogrod\Monad\Either\left;

class EventBuilder
{
    public static function build(array $params): Either
    {
        if ($params['num_max_participants'] < 0) {
            return left(new \LogicException('cant build event because participants number is negative!'));
        }

        return new right(new Event($params['place'], $params['date'], $params['name'], $params['num_max_participants'], $params['description'], $params['organizer'], $params['participants']));
    }
}
