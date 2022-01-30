<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\EmployeeGroupService
 *
 * @property int $id
 * @property int|null $employee_groups_id
 * @property int|null $business_service_id
 * @property-read \App\BusinessService|null $service
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeGroupService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeGroupService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeGroupService query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeGroupService whereBusinessServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeGroupService whereEmployeeGroupsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeGroupService whereId($value)
 * @mixin \Eloquent
 */
class EmployeeGroupService extends Model
{
    // Attributes

    protected $guarded = ['id'];
    protected $table = 'employee_group_services';

    // Relations

    public function service()
    {
        return $this->belongsTo(BusinessService::class, 'business_service_id', 'id', 'business_services');
    }

}
