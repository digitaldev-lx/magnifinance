<?php

namespace App;

use App\Scopes\CompanyScope;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Shetabit\Visitor\Traits\Visitable;

/**
 * App\VendorPage
 *
 * @property int $id
 * @property int|null $company_id
 * @property string $address
 * @property string|null $description
 * @property string|null $primary_contact
 * @property string|null $secondary_contact
 * @property string|null $photos
 * @property string|null $default_image
 * @property string|null $og_image
 * @property string|null $seo_description
 * @property string|null $seo_keywords
 * @property string $map_option
 * @property string $latitude
 * @property string $longitude
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Company|null $company
 * @property-read mixed $images
 * @property-read mixed $photos_without_default_image
 * @method static Builder|VendorPage newModelQuery()
 * @method static Builder|VendorPage newQuery()
 * @method static Builder|VendorPage query()
 * @method static Builder|VendorPage whereAddress($value)
 * @method static Builder|VendorPage whereCompanyId($value)
 * @method static Builder|VendorPage whereCreatedAt($value)
 * @method static Builder|VendorPage whereDefaultImage($value)
 * @method static Builder|VendorPage whereDescription($value)
 * @method static Builder|VendorPage whereId($value)
 * @method static Builder|VendorPage whereLatitude($value)
 * @method static Builder|VendorPage whereLongitude($value)
 * @method static Builder|VendorPage whereMapOption($value)
 * @method static Builder|VendorPage whereOgImage($value)
 * @method static Builder|VendorPage wherePhotos($value)
 * @method static Builder|VendorPage wherePrimaryContact($value)
 * @method static Builder|VendorPage whereSecondaryContact($value)
 * @method static Builder|VendorPage whereSeoDescription($value)
 * @method static Builder|VendorPage whereSeoKeywords($value)
 * @method static Builder|VendorPage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class VendorPage extends Model
{
    use Visitable;
    use SpatialTrait;

    protected $spatialFields = [
        'lat_long'
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
        if(is_null($og_image)){
            return asset('storage/images/logo/9f1c8e217229102acd36ad709c784fc6.png');
        }

        return $og_image;
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
                $reqImage['name'] = $image;
                $reqImage['size'] = filesize(public_path('/user-uploads/vendor-page/'.$this->id.'/'.$image));
                $reqImage['type'] = mime_content_type(public_path('/user-uploads/vendor-page/'.$this->id.'/'.$image));
                $images[] = $reqImage;
            }

        }

        return json_encode($images);
    }

}
