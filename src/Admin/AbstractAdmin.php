<?php


namespace Adeliom\WP\Extensions\Admin;

use Traversable;
use function Sober\Intervention\intervention;

/**
 * Class AbstractAdmin
 * @package Adeliom\WP\Extensions\Admin
 */
abstract class AbstractAdmin
{
    /**
     * Register the admin Block
     * @return void
     */
    public static function register(): void
    {
        if(self::hasOptionPage()){
            $options = self::setupOptionPage();
            if(function_exists("intervention")){
                intervention('add-acf-page', $options["settings"], $options["roles"]);
            }
        }

        register_extended_field_group([
            'title' => static::getTitle(),
            'style' => static::getStyle(),
            'fields' => iterator_to_array(static::getFields()),
            'location' => iterator_to_array(static::getLocation()),
        ]);
    }

    /**
     * Get the admin title
     * @return string
     */
    abstract public static function getTitle(): string;

    /**
     * Get the admin block style
     * @return string
     */
    public static function getStyle(): string
    {
        return "seamless";
    }

    /**
     * Register has option page
     * @return bool
     */
    public static function hasOptionPage(): bool
    {
        return false;
    }

    /**
     * Register option page settings
     * @return array
     */
    public static function setupOptionPage(): array
    {
        return [
            "settings" => "Options",
            "roles" => ['admin', 'editor']
        ];
    }

    /**
     * Get the admin fields list
     * @return Traversable
     */
    abstract public static function getFields(): Traversable;

    /**
     * Get the admin location list
     * @return Traversable
     */
    abstract public static function getLocation(): Traversable;
}
