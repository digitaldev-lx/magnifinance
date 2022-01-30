<?php

namespace App;

use DateTime;
use Carbon\Carbon;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Observers\EmployeeScheduleObserver;

/**
 * App\EmployeeSchedule
 *
 * @property int $id
 * @property int $company_id
 * @property int $employee_id
 * @property string $is_working
 * @property \Illuminate\Support\Carbon|null $start_time
 * @property \Illuminate\Support\Carbon|null $end_time
 * @property string $days
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $employee
 * @method static Builder|EmployeeSchedule newModelQuery()
 * @method static Builder|EmployeeSchedule newQuery()
 * @method static Builder|EmployeeSchedule query()
 * @method static Builder|EmployeeSchedule whereCompanyId($value)
 * @method static Builder|EmployeeSchedule whereCreatedAt($value)
 * @method static Builder|EmployeeSchedule whereDays($value)
 * @method static Builder|EmployeeSchedule whereEmployeeId($value)
 * @method static Builder|EmployeeSchedule whereEndTime($value)
 * @method static Builder|EmployeeSchedule whereId($value)
 * @method static Builder|EmployeeSchedule whereIsWorking($value)
 * @method static Builder|EmployeeSchedule whereStartTime($value)
 * @method static Builder|EmployeeSchedule whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EmployeeSchedule extends Model
{

    protected static function boot()
    {
        parent::boot();

        static::observe(EmployeeScheduleObserver::class);

        $company = company();
        static::addGlobalScope(new CompanyScope);
    }

    protected $dates = ['start_time', 'end_time'];

    public function employee()
    {
        return $this->belongsTo(User::class);
    }

    public function getStartTimeAttribute($value)
    {
        if($this->validateDate($value)){
            return Carbon::createFromFormat('H:i:s', $value)->setTimezone(Company::first()->timezone);
        }

        return '';
    }

    public function getEndTimeAttribute($value)
    {
        if($this->validateDate($value)){
            return Carbon::createFromFormat('H:i:s', $value)->setTimezone(Company::first()->timezone);
        }

        return '';
    }

    public function validateDate($format = 'H:i:s')
    {
        $d = DateTime::createFromFormat('H:i:s', $format);
        return $d && $d->format($format);
    }

}

