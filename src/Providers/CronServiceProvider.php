<?php

namespace Adeliom\WP\Extensions\Providers;

use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;

/**
 * Class CronServiceProvider
 *
 * @package Adeliom\WP\Extensions\Providers
 */
class CronServiceProvider extends ServiceProvider
{
    /**
     * Register all cronjob listed into the config file
     * @param Config $config
     */
    public function boot(Config $config)
    {
        $cronsToRegister = $config->get('cron.register');
        if (is_array($cronsToRegister)) {
            foreach ($cronsToRegister as $cron) {
                $cron::register();
            }
        }
    }
}
