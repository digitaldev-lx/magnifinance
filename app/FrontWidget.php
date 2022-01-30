<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\FrontWidget
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FrontWidget active()
 * @method static \Illuminate\Database\Eloquent\Builder|FrontWidget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FrontWidget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FrontWidget query()
 * @method static \Illuminate\Database\Eloquent\Builder|FrontWidget whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontWidget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontWidget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontWidget whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontWidget whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontWidget whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FrontWidget extends Model
{
    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

}
