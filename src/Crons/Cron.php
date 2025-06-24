<?php

namespace Invoize\Crons;

use Invoize\InvoizePlugin;

abstract class Cron
{

    protected $name;

    protected $schedule;

    public function getName()
    {
        return $this->name;
    }

    public function getSchedule()
    {
        return $this->schedule;
    }

    abstract public function handle();

    public function remove()
    {
        $timestamp = wp_next_scheduled($this->getName());
        wp_unschedule_event($timestamp, $this->getName());
    }

    public function register()
    {
        if (!in_array($this->getSchedule(), array_keys(wp_get_schedules()))) {
            throw new \Exception(esc_html('Invalid Cron Schedule name: ' . $this->getSchedule()));
        }

        foreach (_get_cron_array() as $timestamp => $cron) {
            if (!in_array($this->getName(), array_keys($cron))) {
                add_action($this->getName(), [$this, 'handle']);
                if (!wp_next_scheduled($this->getName())) {
                    wp_schedule_event(time(), $this->getSchedule(), $this->getName());
                }
            }
        }
    }
}
