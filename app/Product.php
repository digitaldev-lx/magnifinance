<?php

namespace App;

use App\Scopes\CompanyScope;
use App\Observers\ProductObserver;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Product
 *
 * @property int $id
 * @property int $location_id
 * @property int $company_id
 * @property string $name
 * @property string $description
 * @property float $price
 * @property float $discount
 * @property string $discount_type
 * @property string|null $image
 * @property string|null $default_image
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookingItem[] $bookingItems
 * @property-read int|null $booking_items_count
 * @property-read mixed $converted_discounted_price
 * @property-read mixed $converted_price
 * @property-read mixed $discounted_price
 * @property-read mixed $formated_discounted_price
 * @property-read mixed $formated_price
 * @property-read mixed $product_image_url
 * @property-read mixed $total_tax_percent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookingItem[] $items
 * @property-read int|null $items_count
 * @property-read \App\Location $location
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ItemTax[] $productTaxes
 * @property-read int|null $product_taxes_count
 * @method static Builder|Product active()
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product query()
 * @method static Builder|Product whereCompanyId($value)
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereDefaultImage($value)
 * @method static Builder|Product whereDescription($value)
 * @method static Builder|Product whereDiscount($value)
 * @method static Builder|Product whereDiscountType($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereImage($value)
 * @method static Builder|Product whereLocationId($value)
 * @method static Builder|Product whereName($value)
 * @method static Builder|Product wherePrice($value)
 * @method static Builder|Product whereStatus($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    // Attributes

    protected static function boot()
    {
        parent::boot();
        static::observe(ProductObserver::class);
        static::addGlobalScope(new CompanyScope);
    }

    protected $appends = [
        'product_image_url',
        'converted_price',
        'converted_discounted_price',
        'formated_price',
        'formated_discounted_price',
        'discounted_price'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function items()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function productTaxes()
    {
        return $this->hasMany(ItemTax::class, 'product_id', 'id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'product_id', 'id');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors

    public function getProductImageUrlAttribute()
    {
        if(is_null($this->default_image) || File::exists('user-uploads/product/'.$this->id.'/'.$this->default_image) == false ) {
            return asset('img/no-image.jpg');
        }

        return asset_url('product/'.$this->id.'/'.$this->default_image);
    }

    public function getImageAttribute($value)
    {
        if (is_array(json_decode($value, true))) {
            return json_decode($value, true);
        }

        return $value;
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

    public function getTotalTaxPercentAttribute()
    {
        if (!$this->productTaxes) {
            return 0;
        }

        $taxPercent = 0;

        foreach ($this->productTaxes as $key => $tax) {
            $taxPercent += $tax->tax->percent;
        }

        return $taxPercent;
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

}
