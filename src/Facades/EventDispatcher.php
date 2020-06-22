<?php

namespace Adeliom\WP\Extensions\Facades;

use Blast\Facades\AbstractFacade;

/**
 * Class EventDispatcher
 * @package Adeliom\WP\Extensions\Facades
 */
class EventDispatcher extends AbstractFacade
{
    /**
     * @return string
     */
    protected static function accessor()
    {
        return 'event_dispatcher';
    }
}
