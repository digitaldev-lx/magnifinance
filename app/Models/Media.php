<?php

namespace App\Models;

use App\Observers\frontSliderObserver;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{

    protected static function boot()
    {
        parent::boot();

        static::observe(frontSliderObserver::class);
    }

    protected $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        /*if (is_null($this->image)) {
            return asset('img/default-avatar-user.png');
        }

        return asset_url('sliders/' . $this->image);*/
        if (is_null($this->image)) {
            return cdn_storage_url("images/default-avatar-user.png");
        }
        return cdn_storage_url($this->image);
    }


}
