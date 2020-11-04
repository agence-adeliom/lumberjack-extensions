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
    public static function paginate($perPage = 10, $args = [])
    {
        global $paged;

        if (!isset($paged) || !$paged) {
            $paged = 1;
        }
        $args = array_merge($args, [
            'posts_per_page' => $perPage,
            'paged' => $paged
        ]);

        // Pagination requires wordpress's query_posts method instead of Timber's.
        query_posts($args);

        return static::query($args);
    }

    public function __isset($field)
    {
        if(!empty(get_field($field, $this->ID))){
            return true;
        }

        return parent::__isset($field);
    }

    public function __get($field)
    {
        if(!empty(get_field($field, $this->ID))){
            return get_field($field, $this->ID);
        }

        return parent::__get($field);
    }
}
