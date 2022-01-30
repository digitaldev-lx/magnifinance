<?php

namespace App\Observers;

use App\Media;
use Illuminate\Support\Facades\File;

class frontSliderObserver
{

    /**
     * Handle the media "deleted" event.
     *
     * @param  \App\Media  $media
     * @return void
     */
    public function deleted(Media $media)
    {
        if(!is_null($media->getRawOriginal('image')))
        {
            $path = public_path('user-uploads/sliders/'.$media->getRawOriginal('image'));

            if($path){
                File::delete($path);
            }
        }
    }

}
