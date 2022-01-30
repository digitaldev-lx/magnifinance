<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Section
 *
 * @property int $id
 * @property string $name
 * @property string $status
 * @method static \Illuminate\Database\Eloquent\Builder|Section active()
 * @method static \Illuminate\Database\Eloquent\Builder|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereStatus($value)
 * @mixin \Eloquent
 */
class Section extends Model
{
    public $timestamps = false;

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

}
