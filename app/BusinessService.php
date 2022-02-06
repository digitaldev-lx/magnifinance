<?php

namespace App;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Observers\BusinessServiceObserver;
use Illuminate\Support\Facades\Auth;
use Shetabit\Visitor\Traits\Visitable;

/**
 * App\BusinessService
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $category_id
 * @property int|null $location_id
 * @property string|null $image
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property float $price
 * @property float $time
 * @property string $time_type
 * @property float $discount
 * @property string $discount_type
 * @property float $net_price
 * @property string|null $default_image
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookingItem[] $bookingItems
 * @property-read int|null $booking_items_count
 * @property-read \App\Category|null $category
 * @property-read \App\Company|null $company
 * @property-read mixed $converted_discounted_price
 * @property-read mixed $converted_price
 * @property-read mixed $discounted_price
 * @property-read mixed $formated_discounted_price
 * @property-read mixed $formated_price
 * @property-read mixed $service_detail_url
 * @property-read mixed $service_image_url
 * @property-read mixed $total_tax_percent
 * @property-read \App\Location|null $location
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ItemTax[] $taxServices
 * @property-read int|null $tax_services_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 * @method static Builder|BusinessService active()
 * @method static Builder|BusinessService activeCompany()
 * @method static Builder|BusinessService newModelQuery()
 * @method static Builder|BusinessService newQuery()
 * @method static Builder|BusinessService query()
 * @method static Builder|BusinessService whereCategoryId($value)
 * @method static Builder|BusinessService whereCompanyId($value)
 * @method static Builder|BusinessService whereCreatedAt($value)
 * @method static Builder|BusinessService whereDefaultImage($value)
 * @method static Builder|BusinessService whereDescription($value)
 * @method static Builder|BusinessService whereDiscount($value)
 * @method static Builder|BusinessService whereDiscountType($value)
 * @method static Builder|BusinessService whereId($value)
 * @method static Builder|BusinessService whereImage($value)
 * @method static Builder|BusinessService whereLocationId($value)
 * @method static Builder|BusinessService whereName($value)
 * @method static Builder|BusinessService whereNetPrice($value)
 * @method static Builder|BusinessService wherePrice($value)
 * @method static Builder|BusinessService whereSlug($value)
 * @method static Builder|BusinessService whereStatus($value)
 * @method static Builder|BusinessService whereTime($value)
 * @method static Builder|BusinessService whereTimeType($value)
 * @method static Builder|BusinessService whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BusinessService extends Model
{

    use Visitable;

    protected static function boot()
    {
        parent::boot();

        static::observe(BusinessServiceObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    protected $appends = [
        'service_image_url',
        'service_detail_url',
        'converted_price',
        'converted_discounted_price',
        'formated_price',
        'formated_discounted_price',
        'discounted_price'
    ];

    public function getServiceImageUrlAttribute()
    {
        if(is_null($this->default_image)){
            return "https://media.istockphoto.com/photos/closeup-portrait-of-her-she-nicelooking-attractive-cheerful-cheery-picture-id1206184490?k=20&m=1206184490&s=612x612&w=0&h=YkCMMkTl_5Q47TYcLjchum1jTzJYmzG7_8kom2DP3lo=";
            return asset('img/default-avatar-user.png');
        }

        return cdn_storage_url($this->default_image);
    }

    public function getImageAttribute($value)
    {
        if (is_array(json_decode($value, true))) {
            return json_decode($value, true);
        }

        return $value;
    }

    public function getServiceDetailUrlAttribute()
    {
        return route('front.serviceDetail', ['serviceSlug' => $this->slug, 'companyId' => $this->company_id]);
    }

    public function getDiscountedPriceAttribute()
    {
        if($this->discount > 0){
            if($this->discount_type == 'fixed'){
                return ($this->price - $this->discount);
            }
            elseif($this->discount_type == 'percent'){
                $discount = (($this->discount / 100) * $this->price);
                return round(($this->price - $discount), 2);
            }
        }

        return $this->price;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeActiveCompany($query)
    {
        return $query->whereHas('company', function($q){
            $q->withoutGlobalScope(CompanyScope::class)->active();
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'service_id', 'id');
    }

    public function taxServices()
    {
        return $this->hasMany(ItemTax::class, 'service_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getConvertedPriceAttribute()
    {
        return currencyConvertedPrice($this->company_id, $this->price);
    }

    public function getConvertedDiscountedPriceAttribute()
    {
        return currencyConvertedPrice($this->company_id, $this->discounted_price);
    }

    public function getFormatedPriceAttribute()
    {
        return currencyFormatter($this->converted_price);
    }

    public function getFormatedDiscountedPriceAttribute()
    {
        return currencyFormatter($this->converted_discounted_price);
    }

    public function getTotalTaxPercentAttribute()
    {
        if (!$this->taxServices) {
            return 0;
        }

        $taxPercent = 0;

        foreach ($this->taxServices as $key => $tax) {
            $taxPercent += $tax->tax->percent;
        }

        return $taxPercent;
    }

}
