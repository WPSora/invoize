<?php

namespace Invoize\Crons\Concerns;

trait HasCrons
{
    protected function registerCronSchedules()
    {
        foreach ($this->getCronSchedules() as $schedule) {
            (new $schedule)->register();
        }
    }

    protected function registerCrons()
    {
        foreach ($this->getCrons() as $cron) {
            (new $cron)->register();
        }
    }

    public function getCronSchedules()
    {
        return $this->cronSchedules;
    }

    public function getCrons()
    {
        return $this->crons;
    }
}
