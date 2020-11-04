<?php
namespace Adeliom\WP\Extensions\ViewModels;

use Adeliom\WP\Extensions\QueryBuilder\QueryBuilder;
use Rareloop\Lumberjack\ViewModel;

class Pagination extends ViewModel
{
    public static function fromQueryBuilder($resultsPerPage, $forPage)
    {
        $pagination = QueryBuilder::getPagination();

        return new static(
            $resultsPerPage,
            $forPage,
            $pagination['current'],
            $pagination['total'],
            $pagination['pages'],
            $pagination['prev'],
            $pagination['next']
        );
    }

    public function __construct($resultsPerPage, $forPage, $current, $total, $pages = [], $prev = [], $next = [])
    {
        $this->pagination = [
            'current' => $current,
            'total' => $total,
            'pages' => $pages,
            'next' => $next,
            'prev' => $prev,
        ];
    }

    public function toArray() : array
    {
        return $this->pagination;
    }
}
