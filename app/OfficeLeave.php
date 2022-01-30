<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\OfficeLeave
 *
 * @property int $id
 * @property string $title
 * @property string $start_date
 * @property string|null $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLeave newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLeave newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLeave query()
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLeave whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLeave whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLeave whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLeave whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLeave whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLeave whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OfficeLeave extends Model
{
    
}
