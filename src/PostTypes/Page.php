<?php

namespace Adeliom\WP\Extensions\PostTypes;

use Adeliom\WP\Extensions\Utils\Types\PostSlug;
use Adeliom\WP\Extensions\Utils\Types\PostType;
use Rareloop\Lumberjack\Exceptions\PostTypeRegistrationException;

class Page extends Post
{
    /**
     * Return the key used to register the post type with WordPress
     * First parameter of the `register_post_type` function:
     * https://codex.wordpress.org/Function_Reference/register_post_type
     */
    public static function getPostType(): string
    {
        return 'page';
    }
}
