<?php


namespace Adeliom\WP\Extensions\Providers;

use Adeliom\WP\Extensions\Admin\AbstractAdmin;
use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;
use ReflectionClass;
use ReflectionException;

/**
 * Class AdminServiceProvider
 *
 * @package Adeliom\WP\Extensions\Providers
 */
class AdminServiceProvider extends ServiceProvider
{
    /**
     * Register all Admin classes
     * @param Config $config
     */
    public function boot(Config $config)
    {
        $adminPath = $this->app->basePath() . "/app/Admin";

        foreach (glob($adminPath . '/*.php') as $file) {
            include($file);
        }

        foreach (get_declared_classes() as $class) {
            if (strpos($class, "App\Admin") !== false) {
                try {
                    $classMeta = new ReflectionClass($class);
                    if ($classMeta->isSubclassOf(AbstractAdmin::class)) {
                        $class::register();
                    }
                } catch (ReflectionException $e) {
                }
            }
        }
    }
}
