<?php

namespace Adeliom\WP\Extensions\Utils\Types;

use WP_Customize_Manager;

/**
 * Class PostTypePageOption
 *
 * Registers an option to select the page for your Custom Post Type in the Customizer.
 */
class PostTypePageOption
{
    /**
     * Post type.
     */
    private string $post_type;

    /**
     * Customizer section.
     */
    private string $customizer_section;

    /**
     * Option name.
     */
    private string $option_name;

    /**
     * PostTypePageOption constructor.
     *
     * @param string $post_type The post type to register the customizer section for.
     * @param string $customizer_section The name of the customizer section where the option to set
     *                                   the page should be added to. The section already needs to
     *                                   exist.
     */
    public function __construct($post_type, $customizer_section)
    {
        $this->post_type          = $post_type;
        $this->customizer_section = $customizer_section;
        $this->option_name        = sprintf('page_for_%s', $this->post_type);
    }

    /**
     * Inits hooks.
     */
    public function init(): void
    {
        if (!is_admin() && !is_customize_preview()) {
            return;
        }

        add_action('customize_register', fn(\WP_Customize_Manager $wp_customize) => $this->registerSettings($wp_customize));

        /**
         * Rewrite rules need to be flushed in the next page load after the Custom Post Type was
         * registered. That’s why we first need to set a transient that we check on the next admin
         * page load.
         */
        add_action(
            sprintf('update_option_%s', $this->option_name),
            fn($old_value, $value) => $this->maybeSetFlushTansient($old_value, $value),
            10,
            2
        );
        add_action('admin_init', fn() => $this->maybeFlushRewriteRules());
    }

    /**
     * Adds Customizer setting and control.
     *
     * @param WP_Customize_Manager $wp_customize Customizer instance.
     */
    public function registerSettings(WP_Customize_Manager $wp_customize): void
    {
        $post_type_object = get_post_type_object($this->post_type);

        $wp_customize->add_setting($this->option_name, [
            'type' => 'option',
        ]);

        $wp_customize->add_control($this->option_name, [
            'label' => sprintf(
            /* translators: Post type label. */
                __('Page for %s', 'mind/types'),
                $post_type_object->label
            ),
            'section' => $this->customizer_section,
            'type' => 'dropdown-pages',
            'allow_addition' => true,
        ]);
    }

    /**
     * Sets transient to flush rewrite rules when option value changes.
     *
     * @param mixed $old_value The old option value.
     * @param mixed $value The new option value.
     */
    public function maybeSetFlushTansient($old_value, $value): void
    {
        if ($old_value !== $value) {
            set_transient(sprintf('flush_%s', $this->option_name), true);
        }
    }

    /**
     * Flushes rewrite rules when transient is set.
     */
    public function maybeFlushRewriteRules(): void
    {
        $transient_name = sprintf('flush_%s', $this->option_name);

        if (get_transient($transient_name)) {
            delete_transient($transient_name);

            add_action('shutdown', function (): void {
                flush_rewrite_rules(false);
            });
        }
    }
}
