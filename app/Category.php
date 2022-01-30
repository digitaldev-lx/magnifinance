<?php

namespace App;

use App\Scopes\CompanyScope;
use App\Observers\CategoryObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Shetabit\Visitor\Traits\Visitable;

/**
 * App\Category
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $image
 * @property string $status
 * @property string $only_blog
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $category_image_url
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BusinessService[] $services
 * @property-read int|null $services_count
 * @method static Builder|Category active()
 * @method static Builder|Category onlyBlog()
 * @method static Builder|Category activeCompanyService()
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category query()
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereImage($value)
 * @method static Builder|Category whereName($value)
 * @method static Builder|Category whereSlug($value)
 * @method static Builder|Category whereStatus($value)
 * @method static Builder|Category whereOnlyBlog($value)
 * @method static Builder|Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    use Visitable;
    protected static function boot()
    {
        parent::boot();

        static::observe(CategoryObserver::class);
    }

    protected $fillable = ['name', 'slug', 'status', 'only_blog','image'];

    protected $appends = [
        'category_image_url'
    ];

    public function getCategoryImageUrlAttribute()
    {
        if (is_null($this->image)) {
            return asset('img/no-image.jpg');
        }

        return asset($this->image);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOnlyBlog($query)
    {
        return $query->where('only_blog', 'yes');
    }

    public function scopeActiveCompanyService($query)
    {
        return $query->whereHas('services', function($q){
            $q->withoutGlobalScope(CompanyScope::class)->activeCompany();
        });
    }

    public function services()
    {
        return $this->hasMany(BusinessService::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
