<?php

namespace Adeliom\WP\Extensions\Providers;

use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;
use Ari\WpHook\HookRegistry;

/**
 * Class HookServiceProvider
 * @package Adeliom\WP\Extensions\Providers
 */
class HookServiceProvider extends ServiceProvider
{
    /**
     * Bind HookRegistry into the container
     */
    public function register()
    {
        $hookRegisty = new HookRegistry();
        $this->app->bind("hooks", $hookRegisty);
        $this->app->bind(HookRegistry::class, $hookRegisty);
    }

    /**
     * Register all hooks listed into the config file
     * @param Config $config
     */
    public function boot(Config $config)
    {
        $hooksToRegister = $config->get('hooks.register');
        if (is_array($hooksToRegister)) {
            foreach ($hooksToRegister as $hook) {
                $this->app->get(HookRegistry::class)->bootstrap($hook);
            }
        }
    }
}
