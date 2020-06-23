<?php

namespace Adeliom\WP\Extensions\Providers;

use Rareloop\Lumberjack\DebugBar\Responses\CssResponse;
use Rareloop\Lumberjack\DebugBar\Responses\JavaScriptResponse;
use Rareloop\Lumberjack\DebugBar\Twig\NodeVisitor;
use Rareloop\Router\Router;
use Timber\Timber;

/**
 * Class DebugBarServiceProvider
 *
 * Setup the debugbar
 * @see https://github.com/Rareloop/lumberjack-debugbar
 * @package Adeliom\WP\Extensions\Providers
 */
class DebugBarServiceProvider extends \Rareloop\Lumberjack\DebugBar\DebugBarServiceProvider
{
    /**
     * @param Router $router
     * @param Timber $timber
     */
    public function boot(Router $router, Timber $timber)
    {
        if ($this->app->has('debugbar')) {
            // Attempt to add the debug bar to the footer
            add_action('wp_footer', [$this, 'echoDebugBar']);

            // Check to make sure that render has been called. Typical reasons it may not:
            // - WP Class name issue => whitescreen
            add_action('wp_before_admin_bar_render', [$this, 'echoDebugBar']);

            // Also catch any custom routes that are sending back html
            add_action('lumberjack_router_response', function ($response) {
                if ($this->isHtmlResponse($response)) {
                    return $this->injectDebugBarCodeIntoResponse($response);
                }

                return $response;
            });

            $router = $this->app->get('router');
            $router->group('debugbar', function ($group) {
                $debugbar = $this->app->get('debugbar');

                $group->get('debugbar.js', function () use ($debugbar) {
                    return new JavaScriptResponse($debugbar->getJavascriptRenderer()->getJsAssetsDump());
                })->name('debugbar.js');

                $group->get('debugbar.css', function () use ($debugbar) {
                    return new CssResponse($debugbar->getJavascriptRenderer()->getCssAssetsDump());
                })->name('debugbar.css');
            });
        }
    }
}
