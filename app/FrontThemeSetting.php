<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\FrontThemeSetting
 *
 * @property int $id
 * @property string $primary_color
 * @property string $secondary_color
 * @property string|null $custom_css
 * @property string|null $logo
 * @property string $seo_description
 * @property string $seo_keywords
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title
 * @property string|null $favicon
 * @property string $customJS
 * @property-read mixed $favicon_url
 * @property-read mixed $logo_url
 * @method static \Illuminate\Database\Eloquent\Builder|FrontThemeSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FrontThemeSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FrontThemeSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|FrontThemeSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontThemeSetting whereCustomCss($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontThemeSetting whereCustomJS($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontThemeSetting whereFavicon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontThemeSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontThemeSetting whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontThemeSetting wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontThemeSetting whereSecondaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontThemeSetting whereSeoDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontThemeSetting whereSeoKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontThemeSetting whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontThemeSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */

class FrontThemeSetting extends Model
{

    public function getLogoUrlAttribute()
    {
        if (is_null($this->logo)) {
            return asset('front/images/logo_white.png');
        }
        return cdn_storage_url($this->logo);
    }

    public function getFaviconUrlAttribute()
    {
        if (is_null($this->logo)) {
            return asset('favicon/apple-icon-57x57.png');
        }
        return cdn_storage_url($this->favicon);
    }

}
