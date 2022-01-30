<?php

namespace App\Observers;

use App\Helper\SearchLog;
use App\BusinessService;
use Illuminate\Support\Facades\File;

class BusinessServiceObserver
{

    public function creating(BusinessService $service)
    {
        if (company()) {
            $service->company_id = company()->id;
        }
    }

    public function created(BusinessService $service)
    {
        SearchLog::createSearchEntry($service->id, 'Service', $service->name, 'admin.business-services.edit', $service->company_id);

    }

    public function updating(BusinessService $service)
    {
        SearchLog::updateSearchEntry($service->id, 'Service', $service->name, 'admin.business-services.edit');
    }

    public function deleted(BusinessService $service)
    {
        SearchLog::deleteSearchEntry($service->id, 'admin.business-services.edit');

        // delete images folder from user-uploads/service directory
        File::deleteDirectory(public_path('user-uploads/service/'.$service->id));
    }

}
