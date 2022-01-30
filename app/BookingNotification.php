<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\BookingNotification
 *
 * @property int $id
 * @property int|null $company_id
 * @property int $duration
 * @property string $duration_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BookingNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BookingNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BookingNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder|BookingNotification whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingNotification whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingNotification whereDurationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingNotification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BookingNotification extends Model
{
    //
}
