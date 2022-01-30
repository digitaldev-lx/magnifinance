<?php

namespace App\Observers;

use App\Deal;
use App\Helper\SearchLog;
use Illuminate\Support\Facades\File;

class DealObserver
{

    public function creating(Deal $deal)
    {
        if (company()) {
            $deal->company_id = company()->id;
        }
    }

    public function created(Deal $deal)
    {
        SearchLog::createSearchEntry($deal->id, 'Deal', $deal->title, 'admin.deals.edit', $deal->company_id);
    }

    public function updating(Deal $deal)
    {
        if($deal->isDirty('image') && !is_null($deal->getRawOriginal('image'))){
            $path = public_path('user-uploads/deal/'.$deal->getRawOriginal('image'));

            if($path){
                File::delete($path);
            }
        }

        SearchLog::updateSearchEntry($deal->id, 'Deal', $deal->title, 'admin.deals.edit');
    }

    public function deleted(Deal $deal)
    {
        if(!is_null($deal->getRawOriginal('image')))
        {
            $path = public_path('user-uploads/deal/'.$deal->getRawOriginal('image'));

            if($path) {
                File::delete($path);
            }

        }
        
        SearchLog::deleteSearchEntry($deal->id, 'admin.deals.edit');
    }

}
