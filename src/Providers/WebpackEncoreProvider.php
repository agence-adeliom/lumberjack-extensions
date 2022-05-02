<?php

declare(strict_types=1);

namespace Adeliom\WP\Extensions\Providers;

use Adeliom\WP\Extensions\Utils\WebpackEncore;
use Rareloop\Lumberjack\Providers\ServiceProvider;

class WebpackEncoreProvider extends ServiceProvider
{
    /**
     * Register any app specific items into the container
     */
    public function register()
    {
        $directory = config("webpack.directory");
        $webpackEncore = new WebpackEncore(sprintf('%s/%s', get_template_directory(), $directory));
        $this->app->bind("webpack", $webpackEncore);
        $this->app->bind(WebpackEncore::class, $webpackEncore);
    }
}
