<?php

namespace Adeliom\WP\Extensions\Providers;

use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;

/**
 * Class AjaxServiceProvider
 *
 * @package Adeliom\WP\Extensions\Providers
 */
class AjaxServiceProvider extends ServiceProvider
{
    /**
     * Register all actions listed into the config file
     * @param Config $config
     */
    public function boot(Config $config)
    {
        $actionsToRegister = $config->get('actions.register');
        if (is_array($actionsToRegister)) {
            foreach ($actionsToRegister as $action) {
                $action::listen();
            }
        }
    }
}
