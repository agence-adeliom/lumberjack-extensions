<?php

namespace Adeliom\WP\Extensions\FlexibleLayout;

use Traversable;
use Extended\ACF\Fields\Field;
use Extended\ACF\Fields\Layout;

/**
 * Class AbstractLayout
 * @package Adeliom\WP\Extensions\FlexibleLayout
 */
abstract class AbstractLayout
{
    /**
     * The layout maker
     *
     * @param string|null $title
     * @param string|null $type
     */
    public static function make(string $title = null, string $key = null, string $type = null): \Extended\ACF\Fields\Layout
    {
        $title ??= static::getTitle();
        $key   = "flex_" . (is_null($key) ? static::getKey() : $key);
        $type ??= static::getType();
        return Layout::make($title, $key ?? null)->layout($type)->fields(iterator_to_array(static::getFields(), false));
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
        return "row";
    }

    /**
     * Return the list of field used on the layout
     *
     * @return \ArrayIterator<int, Field>
     */
    abstract public static function getFields(): Traversable;
}
