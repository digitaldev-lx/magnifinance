<?php

namespace App;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use App\Observers\EmployeeGroupObserver;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\EmployeeGroup
 *
 * @property int $id
 * @property int|null $company_id
 * @property string|null $name
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\EmployeeGroupService[] $services
 * @property-read int|null $services_count
 * @method static Builder|EmployeeGroup newModelQuery()
 * @method static Builder|EmployeeGroup newQuery()
 * @method static Builder|EmployeeGroup query()
 * @method static Builder|EmployeeGroup whereCompanyId($value)
 * @method static Builder|EmployeeGroup whereCreatedAt($value)
 * @method static Builder|EmployeeGroup whereId($value)
 * @method static Builder|EmployeeGroup whereName($value)
 * @method static Builder|EmployeeGroup whereStatus($value)
 * @method static Builder|EmployeeGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EmployeeGroup extends Model
{

    protected static function boot()
    {
        parent::boot();

        static::observe(EmployeeGroupObserver::class);
        static::addGlobalScope(new CompanyScope);
    }

    // Attributes
    protected $guarded = ['id'];
    protected $table = 'employee_groups';


    // Relations

    public function services()
    {
        return $this->hasMany(EmployeeGroupService::class, 'employee_groups_id', 'id');
    }

}
