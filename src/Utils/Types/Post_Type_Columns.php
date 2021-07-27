<?php

namespace Adeliom\WP\Extensions\Utils\Types;

use WP_Query;

/**
 * Class Post_Type_Columns
 */
class Post_Type_Columns
{
    /**
     * Post Type.
     *
     * @var null|string A custom post type slug.
     */
    private $post_type = null;

    /**
     * Column definitions.
     *
     * @var array An array of columns with args.
     */
    private $columns = [];

    /**
     * Post_Type_Columns constructor.
     *
     * @param string $post_type A post type slug.
     * @param array $columns An array of columns to be edited.
     */
    public function __construct($post_type, $columns)
    {
        $this->post_type = $post_type;

        foreach ($columns as $slug => $column) {
            if (false !== $column) {
                // Set defaults for thumbnail.
                if ('thumbnail' === $slug) {
                    $column = wp_parse_args($column, [
                        'title' => __('Featured Image', 'mind/types'),
                        'width' => 80,
                        'height' => 80,
                    ]);
                }

                // Set defaults for each field.
                $column = wp_parse_args($column, [
                    'title' => '',
                    'type' => 'meta',
                    'transform' => null,
                    'sortable' => true,
                    'searchable' => false,
                ]);
            }

            $this->columns[$slug] = $column;
        }
    }

    /**
     * Inits hooks.
     */
    public function init()
    {
        add_filter('manage_edit-' . $this->post_type . '_columns', [$this, 'columns']);
        add_filter('manage_edit-' . $this->post_type . '_sortable_columns', [
            $this,
            'columns_sortable',
        ]);
        add_action('manage_' . $this->post_type . '_posts_custom_column', [
            $this,
            'column_content',
        ], 10, 2);

        if (is_admin()) {
            add_filter('pre_get_posts', [$this, 'search_custom_fields']);
        }
    }

    /**
     * Filters columns for post list view.
     *
     * @param array $columns An array of existing columns.
     *
     * @return array Filtered array.
     */
    public function columns($columns)
    {

        foreach ($this->columns as $slug => $column) {
            if (is_array($column)) {
                $columns[$slug] = $column["title"] ?: $slug;
                continue;
            }
        }

        return $columns;
    }

    /**
     * Filters sortable columns.
     *
     * @param array $columns An array of existing columns.
     *
     * @return array Filtered array.
     */
    public function columns_sortable($columns)
    {
        foreach ($this->columns as $slug => $column) {
            // Remove column when it’s not sortable.
            if (!isset($column['sortable']) || !$column['sortable']) {
                unset($columns[$slug]);
                continue;
            }else{
                $columns[$slug] = $slug;
            }
        }

        return $columns;
    }

    /**
     * Update column contents for post list view.
     *
     * @param string $column_name The column slug.
     * @param int $post_id The post ID.
     */
    public function column_content($column_name, $post_id)
    {
        // Bail out.
        if (empty($this->columns)
            || !in_array($column_name, array_keys($this->columns), true)
        ) {
            return;
        }

        $column = $this->columns[$column_name];

        if ('thumbnail' === $column_name) {
            $src = get_the_post_thumbnail_url($post_id, 'thumbnail');

            if (empty($src)) {
                return;
            }

            $styles = '';

            foreach (['width', 'height'] as $attr) {
                if (isset($column[$attr])) {
                    $styles .= $attr . ':' . $column[$attr] . 'px;';
                }
            }

            if (!empty($styles)) {
                $styles = ' style="' . $styles . '"';
            }

            echo '<img src="' . esc_attr($src) . '"' . $styles . '>';

            return;
        }

        if(isset($column['type'])){
            if ('acf' === $column['type']) {
                $value = get_field($column_name, $post_id);
            } elseif ('meta' === $column['type']) {
                $value = get_post_meta($post_id, $column_name, true);
            }
        }else{
            $value = get_post_meta($post_id, $column_name, true);
        }


        if (isset($column['transform']) && is_callable($column['transform'])) {
            $value = call_user_func($column['transform'], $value, $post_id);
        }

        echo $value;
    }

    /**
     * Includeds searchable custom fields in the search.
     *
     * @param WP_Query $query WordPress query object.
     */
    public function search_custom_fields(WP_Query $query)
    {
        global $typenow;
        $searchterm = $query->query_vars['s'];

        if (!$query->is_main_query() || $typenow !== $this->post_type || empty($searchterm)) {
            return;
        }

        $meta_columns = array_filter($this->columns, function ($column) {
            if(isset($column['type']) && isset($column['searchable'])) {
                return 'meta' === $column['type'] && $column['searchable'];
            }
            return false;
        });

        $meta_query = ['relation' => 'OR'];

        foreach ($meta_columns as $key => $column) {
            $meta_query[] = [
                'key' => $key,
                'value' => $searchterm,
                'compare' => 'LIKE',
            ];
        }

        /**
         * The search parameter needs to be removed from the query, because it will prevent
         * the proper posts from being found.
         */
        //$query->set('s', '');

        $query->set('meta_query', $meta_query);
    }
}
