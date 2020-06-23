<?php


namespace Adeliom\WP\Extensions\FlexibleLayout;

use Traversable;
use WordPlate\Acf\Fields\Layout;

/**
 * Class AbstractLayout
 * @package Adeliom\WP\Extensions\FlexibleLayout
 */
abstract class AbstractLayout
{
    /**
     * The layout maker
     *
     * @param string $title
     * @param string $type
     * @return Layout
     */
    public static function make(string $title = null, string $key = null, string $type = null)
    {
        $title = $title ?? static::getTitle();
        $key   = $key ?? static::getKey();
        $type  = $type ?? static::getType();

        return Layout::make($title, $key ?? null)->layout($type)->fields(iterator_to_array(static::getFields()));
    }

    /**
     * Return the title of the layout
     *
     * @return string
     */
    abstract public static function getTitle(): string;

    /**
     * Return the key of the layout (default: null)
     *
     * @return string|null
     */
    public static function getKey()
    {
        return null;
    }

    /**
     * Return the type of the layout (default: block)
     *
     * @return string
     */
    public static function getType(): string
    {
        return "block";
    }

    /**
     * Return the list of field used on the layout
     *
     * @return Traversable
     */
    abstract public static function getFields(): Traversable;
}
