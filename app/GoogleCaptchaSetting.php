<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\GoogleCaptchaSetting
 *
 * @property int $id
 * @property string $status
 * @property string $v2_status
 * @property string|null $v2_site_key
 * @property string|null $v2_secret_key
 * @property string $v3_status
 * @property string|null $v3_site_key
 * @property string|null $v3_secret_key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCaptchaSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCaptchaSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCaptchaSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCaptchaSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCaptchaSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCaptchaSetting whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCaptchaSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCaptchaSetting whereV2SecretKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCaptchaSetting whereV2SiteKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCaptchaSetting whereV2Status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCaptchaSetting whereV3SecretKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCaptchaSetting whereV3SiteKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleCaptchaSetting whereV3Status($value)
 * @mixin \Eloquent
 */
class GoogleCaptchaSetting extends Model
{
    //
}
