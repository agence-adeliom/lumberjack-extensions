<?php

namespace Adeliom\WP\Extensions\QueryBuilder;

use Adeliom\WP\Extensions\PostTypes\Post;
use Timber\Timber;

class QueryBuilder extends \Rareloop\Lumberjack\QueryBuilder
{
    private $searchTerm;
    private $page;
    protected $postClass = Post::class;

    public function getParameters(): array
    {
        $params = parent::getParameters();

        if (isset($this->page)) {
            $params['paged'] = (int)$this->page;
        }

        return $params;
    }

    /**
     * Use this instead of get()
     */
    public function paginate($perPage = 10, $page = null)
    {
        global $paged;

        if (isset($page)) {
            $paged = $page;
        }

        if (!isset($paged) || !$paged) {
            $paged = 1;
        }

        $this->limit($perPage);
        $this->page($paged);

        query_posts($this->getParameters());

        return $this->get();
    }

    public function page($page)
    {
        $this->page = $page;

        return $this;
    }

    public static function getPagination($prefs = [])
    {
        return Timber::get_pagination($prefs);
    }
}
