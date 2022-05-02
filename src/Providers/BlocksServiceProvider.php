<?php

namespace Adeliom\WP\Extensions\Providers;

use Adeliom\WP\Extensions\Admin\AbstractAdmin;
use Adeliom\WP\Extensions\Blocks\AbstractBlock;
use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;
use ReflectionClass;
use ReflectionException;

/**
 * Class BlocksServiceProvider
 *
 * @package Adeliom\WP\Extensions\Providers
 */
class BlocksServiceProvider extends ServiceProvider
{
    /**
     * Register all Admin classes
     * @param Config $config
     */
    public function boot(Config $config): void
    {
        $adminPath = $this->app->basePath() . "/app/Blocks";

        if (!file_exists($adminPath)) {
            return;
        }

        foreach ($this->getDirContents($adminPath) as $file) {
            $info = pathinfo($file);
            if ($info['extension'] === "php") {
                include($file);
            }
        }

        foreach (get_declared_classes() as $class) {
            if (strpos($class, "App\Blocks") !== false) {
                try {
                    $classMeta = new ReflectionClass($class);
                    if ($classMeta->isSubclassOf(AbstractBlock::class)) {
                        $instance = new $class();

                        if (! $instance->isValid() || ! $instance->isEnabled()) {
                            unset($instance);
                            continue 1;
                        }

                        $instance->init();
                    }
                } catch (ReflectionException $reflectionException) {
                }
            }
        }
    }

    /**
     * @return mixed[]
     */
    private function getDirContents($path): array
    {
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $php_files = new \RegexIterator($rii, '/\.php$/');

        $files = array();
        foreach ($rii as $file) {
            if (!$file->isDir()) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }
}
