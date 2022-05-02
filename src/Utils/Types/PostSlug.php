<?php

namespace Adeliom\WP\Extensions\Utils\Types;

/**
 * Class PostSlug
 *
 * Allows you to register callback functions to customize post slugs.
 */
class PostSlug
{
    /**
     * Post type callbacks.
     *
     * @var array An associative array of post types and their callbacks.
     */
    private array $post_types = [];

    /**
     * Inits hooks.
     */
    public function init(): void
    {
        add_filter('wp_insert_post_data', fn(array $data, array $postarr): array => $this->customizeSlug($data, $postarr), 11, 2);
    }

    /**
     * Registers post type callbacks.
     *
     * @param array $post_types An associative array of post types and their callbacks.
     */
    public function register(array $post_types = []): void
    {
        foreach ($post_types as $post_type => $callback) {
            $this->post_types[$post_type] = [
                'callback' => $callback,
            ];
        }
    }


    /**
     * Customizes the post slug.
     *
     * @param array $data An array of slashed post data.
     * @param array $postarr An array of sanitized, but otherwise unmodified post data.
     *
     * @return mixed[]
     */
    public function customizeSlug(array $data, array $postarr): array
    {
        $bailout_states = ['auto-draft', 'trash'];
        $post_status    = $postarr['post_status'];

        // Bailout if it’s not the right state.
        if (in_array($post_status, $bailout_states, true)) {
            return $data;
        }

        $post_type = $postarr['post_type'];

        // Bailout if no callback could be found.
        if (
            !array_key_exists($post_type, $this->post_types)
            || !is_callable($this->post_types[$post_type]['callback'])
        ) {
            return $data;
        }

        $post_id     = $postarr['ID'];
        $post_slug   = $postarr['post_name'];
        $post_parent = $postarr['post_parent'];

        // Filter post slug through user-defined callback.
        $post_slug = call_user_func($this->post_types[$post_type]['callback'], $post_slug, $postarr, $post_id);

        // Make sure the post slug is sanitized and unique.
        $post_slug = sanitize_title($post_slug);
        $post_slug = wp_unique_post_slug($post_slug, $post_id, $post_status, $post_type, $post_parent);

        $data['post_name'] = $post_slug;

        return $data;
    }
}
