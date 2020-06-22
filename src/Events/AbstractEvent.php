<?php


namespace Adeliom\WP\Extensions\Events;

use Brick\Event\EventDispatcher;
use Rareloop\Lumberjack\Helpers;

/**
 * Class AbstractEvent
 * @package Adeliom\WP\Extensions\Events
 */
abstract class AbstractEvent
{
    /**
     * The event name
     * @return string
     */
    abstract public static function getEvent() : string;

    /**
     * The event priority (default: 10)
     * @return int
     */
    public static function getPriority() : int
    {
        return 10;
    }

    /**
     * The event function
     * @return void
     */
    abstract public static function handle() : void;
}
