<?php

namespace App\Models;

use App\Observers\ArticleObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Shetabit\Visitor\Traits\Visitable;

class Article extends Model
{
    use Visitable;

    protected $guarded = [];

    protected $appends = [
        'article_image_url',
        'limit_title',
        'limit_excerpt',
    ];

    protected $dates = ['published_at'];

    protected static function boot()
    {
        parent::boot();

        static::observe(ArticleObserver::class);

        static::addGlobalScope(new CompanyScope);

    }

    public function getLimitTitleAttribute()
    {
        return Str::limit($this->title, 30, '...');
    }

    public function getLimitExcerptAttribute()
    {
        return Str::limit($this->excerpt, 150, '...');
    }

    public function getIsPublishedAttribute()
    {
       return !is_null($this->published_at);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'approved');
    }

    public function getArticleImageUrlAttribute()
    {
        if(is_null($this->image)){
            return "https://media.istockphoto.com/photos/multi-racial-ethnic-group-of-womans-with-diffrent-types-of-skin-and-picture-id1193184402?k=20&m=1193184402&s=612x612&w=0&h=cXQVcuS46oM0ya0OVH7hpjxPSwW_NdOKb5pM7zLJ2Sw=";
        }

        return cdn_storage_url($this->image);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
