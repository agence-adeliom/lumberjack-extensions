<?php


namespace Adeliom\WP\Extensions\Providers;

use Adeliom\WP\Extensions\Rules\RecaptchaRule;
use Adeliom\WP\Extensions\Twig\RecaptchaExtension;
use Rakit\Validation\RuleQuashException;
use Rareloop\Lumberjack\Providers\ServiceProvider;
use Rareloop\Lumberjack\Validation\Validator;

/**
 * Class RecaptchaServiceProvider
 * Allow Google Recaptchat V2 to Form
 * @see https://github.com/Rareloop/lumberjack-recaptcha
 * @package Adeliom\WP\Extensions\Providers
 */
class RecaptchaServiceProvider extends ServiceProvider
{
    /**
     * @param Validator $validator
     * @throws RuleQuashException
     */
    public function boot(Validator $validator)
    {
        $validator->addValidator('recaptcha', $this->app->get(RecaptchaRule::class));

        add_filter('timber/twig', function ($twig) {
            $twig->addExtension($this->app->get(RecaptchaExtension::class));

            return $twig;
        });

        add_filter('script_loader_tag', [$this, 'addScriptAttributes'], 10, 2);
    }

    /**
     * @param $tag
     * @param $handle
     * @return string|string[]
     */
    public function addScriptAttributes($tag, $handle)
    {
        if ($handle === 'lumberjack-recaptcha') {
            $tag = str_replace('src', 'async defer src', $tag);
        }

        return $tag;
    }
}
