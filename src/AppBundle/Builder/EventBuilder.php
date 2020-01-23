<?php declare(strict_types=1);

namespace AppBundle\Builder;

use AppBundle\Entity\Event;
use DateTime;
use Widmogrod\Monad\Either\Either;
use function Widmogrod\Monad\Either\right;

class EventBuilder
{
    public static function build(array $params): Either
    {
        return right(
            new Event(
                $params['place'],
                new DateTime(),
                $params['name'],
                $params['num_max_participants'],
                $params['description'],
                $params['organizer'],
                $params['participants']
            )
        );
    }
}
