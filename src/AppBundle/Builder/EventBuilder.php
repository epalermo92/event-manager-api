<?php declare(strict_types=1);

namespace AppBundle\Builder;

use AppBundle\Entity\Event;
use AppBundle\Entity\NaturalIdentity;
use Widmogrod\Monad\Either\Either;
use Widmogrod\Monad\Either\Right;
use function Widmogrod\Monad\Either\left;

class EventBuilder
{
    public static function build(array $params): Either
    {
        return new right(new Event($params['place'], new \DateTime(), $params['name'], $params['num_max_participants'], $params['description'], $params['organizer'],$params['participants']));
    }
}
