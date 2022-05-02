<?php

declare(strict_types=1);

namespace Adeliom\WP\Extensions\Twig;

use Adeliom\WP\Extensions\Facades\WebpackEncore;
use Twig\Extension\AbstractExtension;

/**
 * @package App\TwigExtensions
 */
class WebpackEncoreExtension extends AbstractExtension
{
    /**
     * Adds functionality to Twig.
     *
     * @param \Twig\Environment $twig The Twig environment.
     */
    public static function register(\Twig\Environment $twig): \Twig\Environment
    {
        $twig->addFunction(new \Timber\Twig_Function('encore_entry_js_files', [WebpackEncore::class, "jsFiles"]));
        $twig->addFunction(new \Timber\Twig_Function('encore_entry_css_files', [WebpackEncore::class, "cssFiles"]));
        $twig->addFunction(new \Timber\Twig_Function('encore_entry_script_tags', [WebpackEncore::class, "scriptTags"]));
        $twig->addFunction(new \Timber\Twig_Function('encore_entry_link_tags', [WebpackEncore::class, "linkTags"]));
        $twig->addFunction(new \Timber\Twig_Function('asset', [WebpackEncore::class, "asset"]));

        return $twig;
    }
}
