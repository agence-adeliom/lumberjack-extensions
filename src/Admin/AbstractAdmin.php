<?php


namespace Adeliom\WP\Extensions\Admin;

/**
 * Class AbstractAdmin
 * @package Adeliom\WP\Extensions\Admin
 */
abstract class AbstractAdmin
{
    /**
     * Get the admin title
     * @return string
     */
    abstract public static function getTitle(): string;

    /**
     * Get the admin fields list
     * @return \Traversable
     */
    abstract public static function getFields(): \Traversable;

    /**
     * Get the admin location list
     * @return \Traversable
     */
    abstract public static function getLocation(): \Traversable;

    /**
     * Get the admin block style
     * @return string
     */
    public static function getStyle() : string
    {
        return "seamless";
    }

    /**
     * Register the admin Block
     * @return void
     */
    public static function register() : void
    {
        register_extended_field_group([
            'title' => static::getTitle(),
            'style' => static::getStyle(),
            'fields' => iterator_to_array(static::getFields()),
            'location' => iterator_to_array(static::getLocation()),
        ]);
    }
}
