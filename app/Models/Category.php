<?php

namespace App\Models;

use App\Observers\CategoryObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Shetabit\Visitor\Traits\Visitable;

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
        return cdn_storage_url($this->image);
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
