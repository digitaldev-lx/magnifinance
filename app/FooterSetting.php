<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\FooterSetting
 *
 * @property int $id
 * @property array|null $social_links
 * @property string $footer_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FooterSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FooterSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FooterSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|FooterSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FooterSetting whereFooterText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FooterSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FooterSetting whereSocialLinks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FooterSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FooterSetting extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'social_links' => 'array'
    ];
}
