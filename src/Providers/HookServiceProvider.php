<?php

namespace Adeliom\WP\Extensions\Providers;

use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;

/**
 * Class HookServiceProvider
 * @package Adeliom\WP\Extensions\Providers
 */
class HookServiceProvider extends ServiceProvider
{
    /**
     * Register all hooks listed into the config file
     * @param Config $config
     * @throws \ReflectionException
     */
    public function boot(Config $config): void
    {
        $hooksToRegister = $config->get('hooks.register');
        $ns = [];
        foreach ($hooksToRegister as $hook) {
            $class = new \ReflectionClass($hook);
            $hookNs = $class->getNamespaceName();
            if (!in_array($hookNs, $ns, true)) {
                $ns[] = $hookNs;
            }
        }

        add_filter('wp_hook_attributes_registered_namespaces', function () use ($ns) {
            return $ns;
        });

        add_filter('wp_hook_attributes_registered_classes', function (array $registered_classes) use ($hooksToRegister): array {
            return array_merge(
                $registered_classes,
                $hooksToRegister
            );
        });
    }
}
