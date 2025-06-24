<?php

namespace Invoize\Models\States\Recurring;

use Invoize\Classes\Log;
use Invoize\Models\Recurring;
use Invoize\Models\States\Recurring\BaseRecurringState;

class RecurringInactiveState extends BaseRecurringState
{
    public function activate()
    {
        $this->recurring->metas()
            ->where('meta_key', 'recurring_status')
            ->update(['meta_value' => Recurring::ACTIVE]);

        $recurringEndDefault = $this->recurring->metas()
            ->where('meta_key', 'recurring_end_in_default')
            ->value('meta_value');

        $recurringEnd = $this->recurring->metas()
            ->where('meta_key', 'recurring_end_in')
            ->value('meta_value');

        // reset recurring_end_in with the default if recurring is already finish running all
        // if not, continue from previous one
        if (($recurringEnd === '0 time' || $recurringEnd === '0 times') && $recurringEndDefault) {
            $this->recurring->metas()
                ->where('meta_key', 'recurring_end_in')
                ->update(['meta_value' => $recurringEndDefault]);
        }


        $this->saveActionHistory(
            Recurring::INACTIVE,
            Recurring::ACTIVE,
            'update this recurring to active'
        );

        Log::action('Recurring is changed to active. ID: ' . $this->recurring->ID);
    }
}
