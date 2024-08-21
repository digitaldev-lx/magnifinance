<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Shetabit\Visitor\Traits\Visitable;

class VendorPage extends Model
{
    use Visitable;
    use HasSpatial;

    protected $casts = [
        'lat_long' => Point::class,
    ];

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CompanyScope);
    }

    protected $appends = [ 'images', 'photos_without_default_image'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getOgImageAttribute($og_image)
    {
        $globalSetting = GlobalSetting::first();

        if (is_null($og_image)) {
            return $globalSetting->logo_url;
        }

        return cdn_storage_url($og_image);
    }

    public function getPhotosAttribute($value)
    {
        if (is_array(json_decode($value, true))) {
            return json_decode($value, true);
        }

        return $value;
    }

    public function getPhotosWithoutDefaultImageAttribute()
    {
        $photos = $this->photos;

        if($photos){
            return array_merge( array_diff( $photos, [$this->default_image] ));
        }

        return [];
    }

    public function getImagesAttribute()
    {

        $images = [];

        if ($this->photos) {

            foreach ($this->photos as $image) {
                if (\Storage::disk('r2')->exists($image)) {
                    $reqImage['name'] = $image;
                    $reqImage['size'] = \Storage::disk('r2')->size($image);
                    $reqImage['type'] = \Storage::disk('r2')->mimeType($image);
                    $images[] = $reqImage;
                }
            }

        }

        return json_encode($images);
    }

}
