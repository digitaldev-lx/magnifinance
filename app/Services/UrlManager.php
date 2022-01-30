<?php

namespace App\Services;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UrlManager
{
    public function normalizeUrl($url, $secure = true){
        if(!Str::startsWith($url, ['http://', 'https://'])){
            return $secure ? "https://$url" : "http://$url";
        }
        return $url;
    }
}
