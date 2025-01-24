<?php

namespace Invoize\Models\States\Recurring;

use Invoize\Models\States\Interfaces\RecurringStateInterface;
use Carbon\Carbon;

abstract class BaseRecurringState implements RecurringStateInterface
{
    protected $recurring;

    public function __construct($recurring)
    {
        $this->recurring = $recurring;
    }

    public function activate()
    {
        throw new \Exception('Invalid action');
    }

    public function inactivate()
    {
        throw new \Exception('Invalid action');
    }

    public function saveActionHistory(string $from, string $to, string $message)
    {
        $history = $this->recurring->metas()->where('meta_key', 'action_history')->first();
        $user = wp_get_current_user();

        $action = [
            'from' => $from,
            'to' => $to,
            'time' => Carbon::now()->toDateTimeString(),
            'message' => $user->display_name . ' has ' . $message,
            'user' => [
                'name' => $user->display_name,
                'email' => $user->user_email
            ]
        ];

        if (!$history) {
            $meta = ['action_history' => [$action]];
            $this->recurring->setMeta($this->recurring->ID, $meta);
        } else {
            $arr = unserialize($history->meta_value);
            $arr[] = $action;
            $history->meta_value = serialize($arr);
            $history->save();
        }
    }
}
