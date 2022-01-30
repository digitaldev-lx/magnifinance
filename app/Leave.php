<?php

namespace App;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Leave
 *
 * @property int $id
 * @property int $employee_id
 * @property int $company_id
 * @property string $start_date
 * @property string|null $end_date
 * @property string|null $start_time
 * @property string|null $end_time
 * @property string $leave_type
 * @property string $status
 * @property string|null $reason
 * @property string|null $approved_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $employee
 * @method static \Illuminate\Database\Eloquent\Builder|Leave newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Leave newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Leave query()
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereLeaveType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Leave extends Model
{

    public function employee()
    {
        return $this->belongsTo(User::class);
    }

}
