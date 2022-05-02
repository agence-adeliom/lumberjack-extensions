<?php

namespace Adeliom\WP\Extensions\Providers;

use Brick\Event\EventDispatcher;
use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;

/**
 * Class EventServiceProvider
 * @package Adeliom\WP\Extensions\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * Bind the EventDispatcher into the container
     */
    public function register(): void
    {
        $dispatcher = new EventDispatcher();
        $this->app->bind("event_dispatcher", $dispatcher);
        $this->app->bind(EventDispatcher::class, $dispatcher);
    }

    /**
     * Regiter all listenner from config file
     * @param Config $config
     */
    public function boot(Config $config): void
    {
        $eventsToRegister = $config->get('events.listener');
        if (is_array($eventsToRegister)) {
            foreach ($eventsToRegister as $listener) {
                $this->app->get(EventDispatcher::class)->addListener(call_user_func([$listener, "getEvent"]), [$listener, "handle"], call_user_func([$listener, "getPriority"]) ?? 10);
            }
        }
    }
}
