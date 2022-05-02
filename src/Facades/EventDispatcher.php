<?php

namespace Adeliom\WP\Extensions\Facades;

use Blast\Facades\AbstractFacade;

/**
 * Class EventDispatcher
 * @package Adeliom\WP\Extensions\Facades
 */
class EventDispatcher extends AbstractFacade
{
    protected static function accessor(): string
    {
        return 'event_dispatcher';
    }
}
