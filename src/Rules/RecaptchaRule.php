<?php

namespace Adeliom\WP\Extensions\Rules;

use Rakit\Validation\Rule;
use Rareloop\Lumberjack\Config;
use ReCaptcha\ReCaptcha;

/**
 * Class RecaptchaRule
 * @see https://github.com/Rareloop/lumberjack-recaptcha/blob/master/src/RecaptchaRule.php
 * @package Adeliom\WP\Extensions\Rules
 */
class RecaptchaRule extends Rule
{
    /**
     * @var string
     */
    protected $key = 'recaptcha';

    /**
     * @var Config
     */
    protected $config;

    /**
     * RecaptchaRule constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param $value
     * @return bool
     */
    public function check($value)
    {
        $recaptcha = new ReCaptcha($this->config->get('recaptcha.secret'));

        $resp = $recaptcha->setExpectedHostname($this->config->get('recaptcha.hostname'))
            ->verify($value, $_SERVER['REMOTE_ADDR']);

        return $resp->isSuccess();
    }
}
