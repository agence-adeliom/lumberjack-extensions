<?php

namespace Adeliom\WP\Extensions\Utils\Types;

use WP_Post;

/**
 * Class PostTypePageState
 */
class PostTypePageState
{
    /**
     * Post type.
     */
    private string $post_type;

    /**
     * Option name.
     */
    private string $option_name;

    /**
     * PostTypePageState constructor.
     *
     * @param string $post_type The post type to display the post state for.
     */
    public function __construct($post_type)
    {
        $this->post_type   = $post_type;
        $this->option_name = sprintf('page_for_%s', $this->post_type);
    }

    /**
     * Inits hooks.
     */
    public function init(): void
    {
        if (!is_admin()) {
            return;
        }

        add_filter('display_post_states', fn(array $post_states, \WP_Post $post): array => $this->updatePostStates($post_states, $post), 10, 2);
    }

    /**
     * Updates post states with page for event.
     *
     * @param string[] $post_states An array of post display states.
     * @param WP_Post $post The current post object.
     *
     * @return string[] Updates post states.
     */
    public function updatePostStates(array $post_states, $post): array
    {
        $post_type_object = get_post_type_object($this->post_type);

        if (
            'page' === $post->post_type
            && (int)get_option($this->option_name) === $post->ID
        ) {
            $post_states[$this->option_name] = sprintf(
            /* translators: Post type label. */
                __('Page for %s', 'mind/types'),
                $post_type_object->label
            );
        }

        return $post_states;
    }
}
