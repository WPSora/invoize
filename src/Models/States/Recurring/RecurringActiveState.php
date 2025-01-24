<?php

namespace Invoize\Models\States\Recurring;

use Invoize\Models\Recurring;
use Invoize\Models\States\Recurring\BaseRecurringState;

class RecurringActiveState extends BaseRecurringState
{
    public function inactivate()
    {
        $this->recurring->metas()
            ->where('meta_key', 'recurring_status')
            ->update(['meta_value' => Recurring::INACTIVE]);

        $this->saveActionHistory(
            Recurring::ACTIVE,
            Recurring::INACTIVE,
            'update this recurring to inactive'
        );
    }
}
