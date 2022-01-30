<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PackageModules
 *
 * @property int $id
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|PackageModules newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageModules newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageModules query()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageModules whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageModules whereName($value)
 * @mixin \Eloquent
 */
class PackageModules extends Model
{
    protected $guarded = [];
    public $timestamps = false;
}
