<?php

namespace Adeliom\WP\Extensions;

use Adeliom\WP\Extensions\Providers\AdminServiceProvider;
use Adeliom\WP\Extensions\Providers\AjaxServiceProvider;
use Adeliom\WP\Extensions\Providers\CronServiceProvider;
use Adeliom\WP\Extensions\Providers\CustomTaxonomyServiceProvider;
use Adeliom\WP\Extensions\Providers\DebugBarServiceProvider;
use Adeliom\WP\Extensions\Providers\EmailServiceProvider;
use Adeliom\WP\Extensions\Providers\EventServiceProvider;
use Adeliom\WP\Extensions\Providers\HookServiceProvider;
use Adeliom\WP\Extensions\Providers\InterventionServiceProvider;
use Adeliom\WP\Extensions\Providers\RecaptchaServiceProvider;
use Adeliom\WP\Extensions\Providers\ValidationServiceProvider;
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
        DebugBarServiceProvider::class,
        AjaxServiceProvider::class,
        CronServiceProvider::class,
        CustomTaxonomyServiceProvider::class,
        InterventionServiceProvider::class,
        HookServiceProvider::class,
        EventServiceProvider::class,
        AdminServiceProvider::class
    ];

    /**
     * Register the listed providers
     */
    public function register()
    {
        foreach (self::$providers as $provider) {
            $this->app->register($provider);
        }
    }
}
