<?php

namespace Adeliom\WP\Extensions;

use Adeliom\WP\Extensions\Providers\AdminServiceProvider;
use Adeliom\WP\Extensions\Providers\BlocksServiceProvider;
use Adeliom\WP\Extensions\Providers\CronServiceProvider;
use Adeliom\WP\Extensions\Providers\CustomTaxonomyServiceProvider;
use Adeliom\WP\Extensions\Providers\EmailServiceProvider;
use Adeliom\WP\Extensions\Providers\EventServiceProvider;
use Adeliom\WP\Extensions\Providers\HookServiceProvider;
use Adeliom\WP\Extensions\Providers\InterventionServiceProvider;
use Adeliom\WP\Extensions\Providers\RecaptchaServiceProvider;
use Adeliom\WP\Extensions\Providers\TwigExtensionsServiceProvider;
use Adeliom\WP\Extensions\Providers\ValidationServiceProvider;
use Adeliom\WP\Extensions\Providers\WebpackEncoreProvider;
use Neemzy\Twig\Extension\Share\ShareExtension;
use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;

/**
 * Class Extensions
 * @package Adeliom\WP\Extensions
 */
class Extensions extends ServiceProvider
{
    /**
     * @var string[]
     */
    protected static $providers = [
        EmailServiceProvider::class,
        ValidationServiceProvider::class,
        RecaptchaServiceProvider::class,
        CronServiceProvider::class,
        CustomTaxonomyServiceProvider::class,
        InterventionServiceProvider::class,
        HookServiceProvider::class,
        EventServiceProvider::class,
        AdminServiceProvider::class,
        BlocksServiceProvider::class,
        WebpackEncoreProvider::class,
        TwigExtensionsServiceProvider::class
    ];

    /**
     * Register the listed providers
     */
    public function register(): void
    {
        foreach (self::$providers as $provider) {
            $this->app->register($provider);
        }
    }
}
