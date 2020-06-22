<?php

namespace Adeliom\WP\Extensions\Crons;

/**
 * Class WP_CRON
 * @see https://github.com/anthonybudd/WP_Cron
 * @package Adeliom\WP\Extensions\Crons
 */
abstract class WP_CRON
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
     * Generate the job slug
     *
     * @return string
     * @throws \ReflectionException
     */
    public function slug()
    {
        $reflect = new \ReflectionClass($this);
        $class   = $reflect->getShortName();
        return 'wp_cron__' . strtolower($class);
    }

    /**
     * Return the schedule key
     *
     * @return string
     * @throws \ReflectionException
     */
    public function schedule()
    {
        return 'schedule_' . $this->slug();
    }

    /**
     * Calculate the interval for Wordpress compatibility
     *
     * @return float|int
     * @throws \Exception
     */
    public function calculateInterval()
    {

        if (!is_array($this->every)) {
            throw new \Exception("Interval must be an array");
        }

        if (!(count(array_filter(array_keys($this->every), 'is_string')) > 0)) {
            throw new \Exception("WP_Cron::\$interval must be an assoc array");
        }

        $interval    = 0;
        $multipliers = array(
            'seconds' => 1,
            'minutes' => 60,
            'hours' => 3600,
            'days' => 86400,
            'weeks' => 604800,
            'months' => 2628000,
        );

        foreach ($multipliers as $unit => $multiplier) {
            if (isset($this->every[$unit]) && is_int($this->every[$unit])) {
                $interval = $interval + ($this->every[$unit] * $multiplier);
            }
        }

        return $interval;
    }

    /**
     * Function executed by the `cron_schedules` filter
     * @see https://developer.wordpress.org/reference/hooks/cron_schedules/
     *
     * @param $schedules
     * @return mixed
     * @throws \ReflectionException
     */
    public function scheduleFilter($schedules)
    {
        $interval = $this->calculateInterval();

        if (!in_array($this->schedule(), array_keys($schedules))) {
            $schedules[$this->schedule()] = array(
                'interval' => $interval,
                'display' => 'Every ' . floor($interval / 60) . ' minutes',
            );
        }

        return $schedules;
    }

    /**
     * Register the cron task into Wordpress's scheduler
     */
    public static function register()
    {
        $class = get_called_class();
        $self  = new $class;
        $slug  = $self->slug();

        add_filter('cron_schedules', array($self, 'scheduleFilter'));

        if (!wp_next_scheduled($slug)) {
            wp_schedule_event(time(), $self->schedule(), $slug);
        }

        if (method_exists($self, 'handle')) {
            add_action($slug, array($self, 'handle'));
        }
    }
}
