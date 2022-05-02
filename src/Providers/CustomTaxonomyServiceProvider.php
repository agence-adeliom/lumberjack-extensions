<?php

namespace Adeliom\WP\Extensions\Providers;

use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;

/**
 * Class CustomTaxonomyServiceProvider
 *
 * @package Adeliom\WP\Extensions\Providers
 */
class CustomTaxonomyServiceProvider extends ServiceProvider
{
    /**
     * Register all taxonomies listed into the config file
     * @param Config $config
     */
    public function boot(Config $config): void
    {
        $termsToRegister = $config->get('taxonomies.register');

        if (is_array($termsToRegister)) {
            foreach ($termsToRegister as $term) {
                $term::register();
            }
        }
    }
}
