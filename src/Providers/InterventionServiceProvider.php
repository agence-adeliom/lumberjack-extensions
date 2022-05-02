<?php

namespace Adeliom\WP\Extensions\Providers;

use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;

/**
 * Class InterventionServiceProvider
 * @package Adeliom\WP\Extensions\Providers
 */
class InterventionServiceProvider extends ServiceProvider
{
    /**
     * Enable Intervention
     * @param Config $config
     */
    public function boot(Config $config): void
    {
        if (file_exists($this->app->basePath() . "/config/intervention.php")) {
            require_once $this->app->basePath() . "/config/intervention.php";
        }
    }
}
