<?php

namespace Adeliom\WP\Extensions\Actions;

/**
 * Class WP_AJAX
 * @see https://github.com/anthonybudd/WP_AJAX
 * @package Adeliom\WP\Extensions\Actions
 */
abstract class WP_AJAX
{
    /**
     * The action name
     * @var string
     */
    protected $action;

    /**
     * Allow all users and visitor to executed the action
     * @var bool
     */
    protected $public = true;

    /**
     * @var array
     */
    public $request;

    /**
     * @var \WP|null
     */
    public $wp;

    /**
     * @var \WP_User|null
     */
    public $user;

    /**
     * @return mixed
     */
    abstract protected function run();

    /**
     * WP_AJAX constructor.
     */
    public function __construct()
    {
        global $wp;
        $this->wp      = $wp;
        $this->request = $_REQUEST;

        if ($this->is_logged_in()) {
            $this->user = wp_get_current_user();
        }
    }

    public static function boot()
    {
        $class  = self::get_class_name();
        $action = new $class();
        $action->run();
        die();
    }

    /**
     * @throws \Exception
     */
    public static function listen()
    {
        $actionName = self::get_action_name();
        $public = self::is_public();
        $className  = self::get_class_name();
        add_action("wp_ajax_{$actionName}", [$className, 'boot']);

        if (!$public) {
            add_action("wp_ajax_nopriv_{$actionName}", [$className, 'boot']);
        }
    }


    // -----------------------------------------------------
    // UTILITY METHODS
    // -----------------------------------------------------
    /**
     * Return the current class name
     * @return false|string
     */
    public static function get_class_name()
    {
        return get_called_class();
    }

    /**
     * Return the ajax url
     * @return string|void
     */
    public static function ajax_form_url()
    {
        return admin_url('/admin-ajax.php');
    }

    /**
     * Return the action name
     * @return mixed
     * @throws \ReflectionException
     */
    public static function get_action_name()
    {
        // pbrocks renamed since self::get_action_name() otherwise undefined
        // public static function action() {
        $class      = self::get_class_name();
        $reflection = new \ReflectionClass($class);
        $action     = $reflection->newInstanceWithoutConstructor();
        if (!isset($action->action)) {
            throw new \Exception('Public property $action not provied');
        }

        return $action->action;
    }

    /**
     * Get if the action is public
     * @return mixed
     * @throws \ReflectionException
     */
    public static function is_public()
    {
        // pbrocks renamed since self::get_action_name() otherwise undefined
        // public static function action() {
        $class      = self::get_class_name();
        $reflection = new \ReflectionClass($class);
        $action     = $reflection->newInstanceWithoutConstructor();
        if (!isset($action->public)) {
            throw new \Exception('Public property $public not provied');
        }

        return $action->public;
    }



    // -----------------------------------------------------
    // JSONResponse
    // -----------------------------------------------------
    /**
     * Handle a redirect back to referrer
     * @return bool
     */
    public function return_back_json()
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            die();
        }

        return false;
    }

    /**
     * Handle a redirect to url
     * @param $url
     * @param array $params
     */
    public function return_redirect($url, $params = array())
    {
        $url .= '?' . http_build_query($params);
        ob_clean();
        header('Location: ' . $url);
        die();
    }

    /**
     * Handle a json response
     * @param $data
     */
    public function return_json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        die;
    }

    // -----------------------------------------------------
    // Helpers
    // -----------------------------------------------------

    /**
     * Add ajaxurl to boby
     */
    public static function the_ajax_url()
    {
        ?>
        <script type="text/javascript">
            var ajaxurl = '<?php echo admin_url('/admin-ajax.php'); ?>';
        </script>
        <?php
    }

    /**
     * Wordpress action to echo ajaxurl
     */
    public static function wp_head_ajax_url()
    {
        $class = get_called_class();
        $self  = new $class;

        add_action('wp_head', [$self, 'the_ajax_url']);
    }

    /**
     * Get the action URL
     * @param array $params
     * @return string
     */
    public static function url($params = array())
    {
        $params = http_build_query(
            array_merge(
                array(
                    'action' => (new static())->action,
                ),
                $params
            )
        );

        return admin_url('/admin-ajax.php') . '?' . $params;
    }

    /**
     * @return bool
     */
    public function is_logged_in()
    {
        return is_user_logged_in();
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        if (isset($this->request[$key])) {
            return true;
        }

        return false;
    }

    /**
     * [get description]
     *
     * @param string $key [description]
     * @param string $default [description]
     * @return string
     */
    public function get($key, $default = null, $stripslashes = true)
    {
        if ($this->has($key)) {
            if ($stripslashes) {
                return stripslashes($this->request[$key]);
            }
            return $this->request[$key];
        }
        return $default;
    }

    /**
     * @param null $request_type
     * @return bool|mixed
     */
    public function request_type($request_type = null)
    {
        if (!is_null($request_type)) {
            if (is_array($request_type)) {
                return in_array($_SERVER['REQUEST_METHOD'], array_map('strtoupper', $request_type));
            }

            return ($_SERVER['REQUEST_METHOD'] === strtoupper($request_type));
        }

        return $_SERVER['REQUEST_METHOD'];
    }
}
