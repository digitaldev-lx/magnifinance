<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrontThemeSetting extends Model
{

    public function getLogoUrlAttribute()
    {
        if (is_null($this->logo)) {
            return asset('front/images/logo_white.png');
        }
        return cdn_storage_url($this->logo);
    }

    public function getFaviconUrlAttribute()
    {
        if (is_null($this->logo)) {
            return asset('favicon/apple-icon-57x57.png');
        }
        return cdn_storage_url($this->favicon);
    }

}
