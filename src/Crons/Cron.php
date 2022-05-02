<?php

namespace Adeliom\WP\Extensions\Crons;

use Exception;
use ReflectionClass;
use ReflectionException;

/**
 * Class WP_CRON
 * @see https://github.com/anthonybudd/WP_Cron
 * @package Adeliom\WP\Extensions\Crons
 */
abstract class Cron
{
    /**
     * Setup de interval between executions
     *
     * @var int[]
     */
    protected $every = array(
        'seconds' => 0,
        'minutes' => 0,
        'hours' => 0,
        'days' => 0,
        'weeks' => 0,
        'months' => 0,
    );

    /**
     * Register the cron task into Wordpress's scheduler
     */
    public static function register()
    {
        $class = static::class;
        $self  = new $class();
        $slug  = $self->slug();

        add_filter('cron_schedules', fn($schedules) => $self->scheduleFilter($schedules));

        if (!wp_next_scheduled($slug)) {
            wp_schedule_event(time(), $self->schedule(), $slug);
        }

        if (method_exists($self, 'handle')) {
            add_action($slug, array($self, 'handle'));
        }
    }

    /**
     * Function executed by the `cron_schedules` filter
     * @see https://developer.wordpress.org/reference/hooks/cron_schedules/
     *
     * @param $schedules
     * @return mixed
     * @throws ReflectionException
     */
    public function scheduleFilter($schedules)
    {
        $interval = $this->calculateInterval();

        if (!array_key_exists($this->schedule(), $schedules)) {
            $schedules[$this->schedule()] = array(
                'interval' => $interval,
                'display' => 'Every ' . floor($interval / 60) . ' minutes',
            );
        }

        return $schedules;
    }

    /**
     * Calculate the interval for Wordpress compatibility
     *
     * @return float|int
     * @throws Exception
     */
    public function calculateInterval(): int
    {

        if (!is_array($this->every)) {
            throw new Exception("Interval must be an array");
        }

        if (count(array_filter(array_keys($this->every), 'is_string')) <= 0) {
            throw new Exception("WP_Cron::\$interval must be an assoc array");
        }

        $interval    = 0;
        $multipliers = array(
            'seconds' => 1,
            'minutes' => 60,
            'hours' => 3600,
            'days' => 86400,
            'weeks' => 604800,
            'months' => 2_628_000,
        );

        foreach ($multipliers as $unit => $multiplier) {
            if (isset($this->every[$unit]) && is_int($this->every[$unit])) {
                $interval += $this->every[$unit] * $multiplier;
            }
        }

        return $interval;
    }

    /**
     * Return the schedule key
     *
     * @throws ReflectionException
     */
    public function schedule(): string
    {
        return 'schedule_' . $this->slug();
    }

    /**
     * Generate the job slug
     *
     * @throws ReflectionException
     */
    public function slug(): string
    {
        $reflect = new ReflectionClass($this);
        $class   = $reflect->getShortName();
        return 'wp_cron__' . strtolower($class);
    }
}
