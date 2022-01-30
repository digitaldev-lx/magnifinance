<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Observers\frontSliderObserver;

/**
 * App\Media
 *
 * @property int $id
 * @property string|null $image
 * @property string $have_content
 * @property string|null $subheading
 * @property string|null $heading
 * @property string|null $content
 * @property string|null $action_button
 * @property string|null $url
 * @property string|null $open_tab
 * @property string|null $content_alignment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $image_url
 * @method static \Illuminate\Database\Eloquent\Builder|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Media query()
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereActionButton($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereContentAlignment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereHaveContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereHeading($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereOpenTab($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereSubheading($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUrl($value)
 * @mixin \Eloquent
 */
class Media extends Model
{

    protected static function boot()
    {
        parent::boot();

        static::observe(frontSliderObserver::class);
    }

    protected $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        if (is_null($this->image)) {
            return asset('img/default-avatar-user.png');
        }
        
        return asset_url('sliders/' . $this->image);
    }

}
