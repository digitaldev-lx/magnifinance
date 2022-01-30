<?php

namespace App;

use App\Observers\ArticleObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Shetabit\Visitor\Traits\Visitable;

/**
 * App\Article
 *
 * @property int $id
 * @property int|null $company_id
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string $content
 * @property string $status
 * @property string|null $keywords
 * @property string|null $seo_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $published_at
 * @method static Builder|Article published()
 * @method static Builder|Article whereId($value)
 * @method static Builder|Article whereSlug($value)
 * @method static Builder|Article whereStatus($value)
 * @method static Builder|Article whereTitle($value)
 * @method static Builder|Article whereUpdatedAt($value)
 * @mixin \Eloquent
 */

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
        if (is_null($this->image) || $this->image == '') {
            return asset('img/no-image.jpg');
        }
//
        return asset($this->image);
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
