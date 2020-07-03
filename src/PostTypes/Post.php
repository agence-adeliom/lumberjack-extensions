<?php


namespace Adeliom\WP\Extensions\PostTypes;

use Adeliom\WP\Extensions\Utils\Types\Post_Slug;
use Adeliom\WP\Extensions\Utils\Types\Post_Type;
use Rareloop\Lumberjack\Exceptions\PostTypeRegistrationException;
use Rareloop\Lumberjack\Post as BasePost;

class Post extends BasePost
{

    public static function register()
    {
        $postType = static::getPostType();
        $config   = static::getPostTypeConfig();

        if (empty($postType) || $postType === 'post') {
            throw new PostTypeRegistrationException('Post type not set');
        }

        if (empty($config)) {
            throw new PostTypeRegistrationException('Config not set');
        }

        Post_Type::register([$postType => $config]);

        self::customSlug(static::getPostTypeCustomSlug());
    }

    /**
     * @param $function
     */
    public static function customSlug($function)
    {
        $postType = static::getPostType();

        $post_slugs = new Post_Slug();
        $post_slugs->init();

        $post_slugs->register([
            $postType => $function
        ]);
    }

    protected static function getPostTypeCustomSlug()
    {
        return function ($post_slug, $post_data, $post_id) {
            return $post_slug;
        };
    }
}
