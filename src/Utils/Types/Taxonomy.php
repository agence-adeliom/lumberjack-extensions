<?php

namespace Adeliom\WP\Extensions\Utils\Types;

/**
 * Class Taxonomy
 */
class Taxonomy
{
    /**
     * Register taxonomies based on an array definition.
     *
     * @param array $taxonomies {
     *      An array of arrays for taxonomies, where the name of the taxonomy is the key of an array.
     *
     * @type string $name_singular Singular name for taxonomy.
     * @type string $name_plural Plural name for taxonomy.
     * @type array $for_post_types The array of post types you want to register the taxonomy for.
     * @type array $args Arguments that get passed to taxonomy registration.
     * }
     */
    public static function register(array $taxonomies = []): void
    {
        foreach ($taxonomies as $taxonomy => $args) {
            $args = self::parseArgs($args);
            self::registerExtensions($taxonomy, $args);

            $for_post_types = $args['for_post_types'];

            // Defaults for taxonomy registration.
            $args = wp_parse_args($args['args'], [
                'public' => false,
                'hierarchical' => false,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_tag_cloud' => false,
            ]);

            register_taxonomy($taxonomy, $for_post_types, $args);
        }
    }

    /**
     * Adds missing arguments for taxonomy.
     *
     * @param array $args An array of arguments.
     *
     * @return mixed[]
     * @since 2.2.0
     */
    private static function parseArgs(array $args): array
    {
        if (isset($args['name_singular']) && !isset($args['name_plural'])) {
            $args['name_plural'] = $args['name_singular'];
        }

        return $args;
    }

    /**
     * Registers extensions.
     *
     * @param string $taxonomy The taxonomy name.
     * @param array $args Arguments for the taxonomy.
     * @since 2.2.0
     *
     */
    private static function registerExtensions(string $taxonomy, array $args): void
    {
        if (isset($args['name_singular'])) {
            (new TaxonomyLabels($taxonomy, $args['name_singular'], $args['name_plural']))->init();
        }
    }

    /**
     * Updates settings for a taxonomy.
     *
     * Here, you use the same settings that you also use for the `register()` funciton.
     *
     * Run this function before the `init` hook.
     *
     * @param array $taxonomies An associative array of post types and its arguments that should be updated. See the
     *                          `register()` function for all the arguments that you can use.
     * @since 2.2.0
     *
     * @see register_taxonomy()
     */
    public static function update(array $taxonomies = []): void
    {
        foreach ($taxonomies as $taxonomy => $args) {
            $args = self::parseArgs($args);
            self::registerExtensions($taxonomy, $args);

            if (isset($args['args'])) {
                add_filter('register_taxonomy_args', function ($defaults, $name) use ($taxonomy, $args) {
                    if ($taxonomy !== $name) {
                        return $defaults;
                    }

                    return wp_parse_args($args['args'], $defaults);
                }, 10, 2);
            }
        }
    }

    /**
     * Renames a taxonomy.
     *
     * Run this function before the `init` hook.
     *
     * @param string $taxonomy The taxonomy to rename.
     * @param string $name_singular The new singular name.
     * @param string $name_plural The new plural name.
     * @since 2.2.0
     *
     */
    public static function rename(string $taxonomy, string $name_singular, string $name_plural): void
    {
        if (!taxonomy_exists($taxonomy)) {
            return;
        }

        (new TaxonomyLabels($taxonomy, $name_singular, $name_plural))->init();
    }
}
