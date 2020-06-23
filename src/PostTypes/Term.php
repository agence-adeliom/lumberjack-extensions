<?php


namespace Adeliom\WP\Extensions\PostTypes;

use Adeliom\WP\Extensions\Utils\Types\Taxonomy;
use Rareloop\Lumberjack\Exceptions\PostTypeRegistrationException;
use Spatie\Macroable\Macroable;
use Timber\Term as TimberTerm;

class Term extends TimberTerm
{
    use Macroable {
        Macroable::__call as __macroableCall;
        Macroable::__callStatic as __macroableCallStatic;
    }

    public function __construct($tid = null, $tax = '', $preventTimberInit = false)
    {
        /**
         * There are occasions where we do not want the bootstrap the data. At the moment this is
         * designed to make Query Scopes possible
         */
        if (!$preventTimberInit) {
            parent::__construct($tid, $tax);
        }
    }

    public static function __callStatic($name, $arguments)
    {
        if (static::hasMacro($name)) {
            return static::__macroableCallStatic($name, $arguments);
        }

        trigger_error('Call to undefined method ' . __CLASS__ . '::' . $name . '()', E_USER_ERROR);
    }

    public static function register()
    {
        $term   = static::getTerm();
        $config = static::getTaxonomyConfig();

        if (empty($term) || $term === 'category') {
            throw new PostTypeRegistrationException('Term not set');
        }

        if (empty($config)) {
            throw new PostTypeRegistrationException('Config not set');
        }

        Taxonomy::register([$term => $config]);
    }

    /**
     * Return the key used to register the taxonomy with WordPress
     * First parameter of the `register_taxonomy` function:
     * https://codex.wordpress.org/Function_Reference/register_taxonomy
     *
     * @return string
     */
    public static function getTerm()
    {
        return 'category';
    }

    /**
     * Return the config to use to register the post type with WordPress
     * Second parameter of the `register_post_type` function:
     * https://codex.wordpress.org/Function_Reference/register_post_type
     *
     * @return array|null
     */
    protected static function getTaxonomyConfig()
    {
        return null;
    }

    public function __call($name, $arguments)
    {
        if (static::hasMacro($name)) {
            return $this->__macroableCall($name, $arguments);
        }

        return parent::__call($name, $arguments);
    }
}
