<?php

namespace Adeliom\WP\Extensions\Providers;

use Dugajean\WpHookAnnotations\HookRegistry;
use Neemzy\Twig\Extension\Share\ShareExtension;
use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;

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
                "mix_any",
                "camel_case",
                "kebab_case",
                "snake_case",
                "ends_with",
                "starts_with",
                "str_contains",
                "str_is",
                "str_limit",
                "str_random",
                "str_slug",
                "studly_case",
                "title_case",
            ], $functionsToRegister ?? []);

            $twig->addExtension(new \Umpirsky\Twig\Extension\PhpFunctionExtension($phpFunctions));
            $twig->addExtension(new \DPolac\TwigLambda\LambdaExtension());
            $twig->addExtension(new \Aaronadal\TwigListLoop\Twig\TwigExtension());

            return $twig;
        });

        $extensionsToRegister = $config->get('twig.extensions');
        if (is_array($extensionsToRegister)) {
            foreach ($extensionsToRegister as $extension) {
                add_filter("timber/twig", [$extension, "register"], 10, 1);
            }
        }
    }
}
