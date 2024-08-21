<?php

namespace App\Observers;

use App\Models\OfflinePlanChange;
use App\Notifications\OfflinePackageChangeRequest;
use App\User;
use Illuminate\Support\Facades\Notification;

class OfflinePlanChangeObserver
{

    public function created(OfflinePlanChange $offlinePlanChange)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $company = company();

            $generatedBy = User::withoutGlobalScopes(['company'])->whereNull('company_id')->first();

            Notification::send($generatedBy, new OfflinePackageChangeRequest($company, $offlinePlanChange));
        }
    }

}
