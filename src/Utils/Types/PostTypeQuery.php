<?php

namespace Adeliom\WP\Extensions\Utils\Types;

use WP_Query;

/**
 * Class PostTypeQuery
 */
class PostTypeQuery
{
    /**
     * Post Type.
     *
     * @var null|string A custom post type slug.
     */
    private ?string $post_type = null;

    /**
     * Query args.
     *
     * @var array An array of query args.
     */
    private array $query_args = [];

    /**
     * Custom_PostTypeQuery constructor.
     *
     * @param string $post_type The custom post type.
     * @param array $query_args Arguments used for WP_Query.
     */
    public function __construct($post_type, $query_args)
    {
        $this->post_type  = $post_type;
        $this->query_args = $this->parseQueryArgs($query_args);
    }

    /**
     * Parses the query args.
     *
     * Returns an associative array with key `frontend` and `backend` that each contain query
     * settings.
     *
     * @param array $args An array of query args.
     *
     * @return array An array of query args.
     * @since 2.2.0
     *
     */
    public function parseQueryArgs(array $args): array
    {
        $query_args = [
            'frontend' => $args,
            'backend' => $args,
        ];

        if (isset($args['frontend']) || isset($args['backend'])) {
            foreach (['frontend', 'backend'] as $query_type) {
                $query_args[$query_type] = $args[$query_type] ?? [];
            }
        }

        return $query_args;
    }

    /**
     * Inits hooks.
     */
    public function init(): void
    {
        add_action('pre_get_posts', fn(\WP_Query $query) => $this->preGetPosts($query));
    }

    /**
     * Alters the query.
     *
     * @param WP_Query $query A WP_Query object.
     */
    public function preGetPosts($query): void
    {
        global $typenow;

        /**
         * Check if we should modify the query.
         *
         * As a hint for for future condition updates: We can’t use $query->is_post_type_archive(),
         * because some post_types have 'has_archive' set to false.
         */
        if (!is_admin()) {
            if (
                // Special case for post in a page_for_posts setting.
                ('post' === $this->post_type && !$query->is_home())
                // All other post types.
                || ('post' !== $this->post_type && $this->post_type !== $query->get('post_type'))
            ) {
                return;
            }
        } elseif (!$query->is_main_query() || $typenow !== $this->post_type) {
            return;
        }

        // Differ between frontend and backend queries.
        $query_args = $this->query_args[is_admin() ? 'backend' : 'frontend'];

        if (empty($query_args)) {
            return;
        }

        // Set query args.
        foreach ($query_args as $key => $arg) {
            $query->set($key, $arg);
        }
    }
}
