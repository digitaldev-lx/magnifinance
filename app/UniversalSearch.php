<?php

namespace App;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\UniversalSearch
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $location_id
 * @property string $searchable_id
 * @property string $searchable_type
 * @property string $title
 * @property string $route_name
 * @property int|null $count
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|UniversalSearch newModelQuery()
 * @method static Builder|UniversalSearch newQuery()
 * @method static Builder|UniversalSearch query()
 * @method static Builder|UniversalSearch whereCompanyId($value)
 * @method static Builder|UniversalSearch whereCount($value)
 * @method static Builder|UniversalSearch whereCreatedAt($value)
 * @method static Builder|UniversalSearch whereId($value)
 * @method static Builder|UniversalSearch whereLocationId($value)
 * @method static Builder|UniversalSearch whereRouteName($value)
 * @method static Builder|UniversalSearch whereSearchableId($value)
 * @method static Builder|UniversalSearch whereSearchableType($value)
 * @method static Builder|UniversalSearch whereTitle($value)
 * @method static Builder|UniversalSearch whereType($value)
 * @method static Builder|UniversalSearch whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UniversalSearch extends Model
{
    protected $table = 'universal_searches';
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

}
