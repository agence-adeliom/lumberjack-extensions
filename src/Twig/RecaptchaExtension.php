<?php

namespace Adeliom\WP\Extensions\Twig;

use Rareloop\Lumberjack\Config;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class RecaptchaExtension
 * @see https://github.com/Rareloop/lumberjack-recaptcha/blob/master/src/Twig/RecaptchaExtension.php
 * @package Adeliom\WP\Extensions\Twig
 */
class RecaptchaExtension extends AbstractExtension
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * RecaptchaExtension constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('recaptcha', [$this, 'recaptcha']),
        ];
    }

    /**
     * @return string
     */
    public function recaptcha()
    {
        wp_enqueue_script('lumberjack-recaptcha', 'https://www.google.com/recaptcha/api.js', [], 'v2');

        return '<div class="g-recaptcha" data-sitekey="' . $this->config->get('recaptcha.key') . '"></div>';
    }
}
