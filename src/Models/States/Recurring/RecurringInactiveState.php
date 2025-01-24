<?php

namespace Invoize\Models\States\Recurring;

use Invoize\Models\Recurring;
use Invoize\Models\States\Recurring\BaseRecurringState;

class RecurringInactiveState extends BaseRecurringState
{
    public function activate()
    {
        $this->recurring->metas()
            ->where('meta_key', 'recurring_status')
            ->update(['meta_value' => Recurring::ACTIVE]);

        // reset recurring_end_in with the default
        $recurringMeta = $this->recurring->metas()->where('meta_key', 'recurring')->first();
        $recurring = unserialize($recurringMeta->meta_value);
        $this->recurring->metas()
            ->where('meta_key', 'recurring_end_in')
            ->update(['meta_value' => $recurring['end']]);

        $this->saveActionHistory(
            Recurring::INACTIVE,
            Recurring::ACTIVE,
            'update this recurring to active'
        );
    }
}
