<?php

namespace App\Observers;

use App\Helper\SearchLog;
use App\Location;

class LocationObserver
{

    public function created(Location $location)
    {
        SearchLog::createSearchEntry($location->id, 'Location', $location->name, 'superadmin.locations.edit');

    }

    public function updating(Location $location)
    {
        SearchLog::updateSearchEntry($location->id, 'Location', $location->name, 'superadmin.locations.edit');
    }

    public function deleted(Location $location)
    {
        SearchLog::deleteSearchEntry($location->id, 'superadmin.locations.edit');
    }

}
