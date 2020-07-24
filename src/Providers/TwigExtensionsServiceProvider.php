<?php

namespace Adeliom\WP\Extensions\Providers;

use Dugajean\WpHookAnnotations\HookRegistry;
use Neemzy\Twig\Extension\Share\ShareExtension;
use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;
use HelloNico\Twig\DumpExtension;
use Ajgl\Twig\Extension\BreakpointExtension;
use Djboris88\Twig\Extension\CommentedIncludeExtension;

/**
 * Class TwigExtensionsServiceProvider
 * @package Adeliom\WP\Extensions\Providers
 */
class TwigExtensionsServiceProvider extends ServiceProvider
{
    /**
     * Register all extensions listed into the config file
     * @param Config $config
     */
    public function boot(Config $config)
    {
        add_filter('timber/twig', function ($twig) use ($config) {
            $functionsToRegister = $config->get('twig.allowed_functions');
            $twig->addExtension(ShareExtension::getInstance());

            $phpFunctions = array_merge([
                "mix",
                "mix_any"
            ], $functionsToRegister ?? []);

            $twig->addExtension(new \Umpirsky\Twig\Extension\PhpFunctionExtension($phpFunctions));
            $twig->addExtension(new \DPolac\TwigLambda\LambdaExtension());
            $twig->addExtension(new \Aaronadal\TwigListLoop\Twig\TwigExtension());

            if (defined("WP_DEBUG") && WP_DEBUG) {
                $twig->addExtension(new CommentedIncludeExtension());
                $twig->addExtension(new DumpExtension());
                $twig->addExtension(new BreakpointExtension());
            }

            return $twig;
        });

        if (defined("WP_DEBUG") && WP_DEBUG) {
            /**
             * Adding a second filter to cover the `Timber::render()` case, when the
             * template is not loaded through the `include` tag inside a twig file
             */
            add_filter( 'timber/output', function( $output, $data, $file ) {
                return "\n<!-- Begin output of '" . $file . "' -->\n" . $output . "\n<!-- / End output of '" . $file . "' -->\n";
            }, 10, 3 );
        }

        $extensionsToRegister = $config->get('twig.extensions');
        if (is_array($extensionsToRegister)) {
            foreach ($extensionsToRegister as $extension) {
                add_filter("timber/twig", [$extension, "register"], 10, 1);
            }
        }
    }
}
