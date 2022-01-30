<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SmsSetting
 *
 * @property int $id
 * @property string $nexmo_status
 * @property string|null $nexmo_key
 * @property string|null $nexmo_secret
 * @property string|null $nexmo_from
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $msg91_status
 * @property string|null $msg91_key
 * @property string|null $msg91_from
 * @method static \Illuminate\Database\Eloquent\Builder|SmsSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsSetting whereMsg91From($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsSetting whereMsg91Key($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsSetting whereMsg91Status($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsSetting whereNexmoFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsSetting whereNexmoKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsSetting whereNexmoSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsSetting whereNexmoStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SmsSetting extends Model
{
    //
}
