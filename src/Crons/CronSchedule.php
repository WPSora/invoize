<?php

namespace Invoize\Crons;

class CronSchedule
{
    protected $name;

    protected $interval;

    protected $display;

    public function getName()
    {
        return $this->name;
    }

    public function getInterval()
    {
        return $this->interval;
    }

    public function getDisplay()
    {
        return $this->display;
    }

    public function register()
    {
        if (!in_array($this->getName(), array_keys(wp_get_schedules()))) {
            add_filter('cron_schedules', function ($schedules) {
                $schedules[$this->getName()] = [
                    'interval' => $this->getInterval(), // in seconds
                    'display' => $this->getDisplay()
                ];
                return $schedules;
            });
        }
    }
}
