<?php

namespace Adeliom\WP\Extensions\Providers;

use Adeliom\WP\Extensions\Admin\AbstractAdmin;
use Adeliom\WP\Extensions\Hooks\FontLibraryHooks;
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
    public function boot(Config $config): void
    {
        // WordPress 7.0 : désactivation de la Font Library (menu + accès direct).
        add_action('admin_menu', [FontLibraryHooks::class, "removeFontLibraryMenu"], 999, 0);
        add_action('admin_init', [FontLibraryHooks::class, "blockFontLibraryAccess"], 10, 0);

        $adminPath = $this->app->basePath() . "/app/Admin";

        foreach ($this->getDirContents($adminPath) as $file) {
            include($file);
        }

        foreach (get_declared_classes() as $class) {
            if (strpos($class, "App\Admin") !== false) {
                try {
                    $classMeta = new ReflectionClass($class);
                    if ($classMeta->isSubclassOf(AbstractAdmin::class)) {
                        $class::register();
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
