<?php

namespace App;

use Carbon\Carbon;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Auth;
use App\Observers\BookingTimeObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\BookingTime
 *
 * @property int $id
 * @property int|null $company_id
 * @property string $day
 * @property string $start_time
 * @property string $end_time
 * @property string $multiple_booking
 * @property int $max_booking
 * @property int $per_day_max_booking
 * @property int $per_slot_max_booking
 * @property string $status
 * @property int $slot_duration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $utc_end_time
 * @property-read mixed $utc_start_time
 * @method static Builder|BookingTime newModelQuery()
 * @method static Builder|BookingTime newQuery()
 * @method static Builder|BookingTime query()
 * @method static Builder|BookingTime whereCompanyId($value)
 * @method static Builder|BookingTime whereCreatedAt($value)
 * @method static Builder|BookingTime whereDay($value)
 * @method static Builder|BookingTime whereEndTime($value)
 * @method static Builder|BookingTime whereId($value)
 * @method static Builder|BookingTime whereMaxBooking($value)
 * @method static Builder|BookingTime whereMultipleBooking($value)
 * @method static Builder|BookingTime wherePerDayMaxBooking($value)
 * @method static Builder|BookingTime wherePerSlotMaxBooking($value)
 * @method static Builder|BookingTime whereSlotDuration($value)
 * @method static Builder|BookingTime whereStartTime($value)
 * @method static Builder|BookingTime whereStatus($value)
 * @method static Builder|BookingTime whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BookingTime extends Model
{

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
        static::observe(BookingTImeObserver::class);

    }

    protected $guarded = ['id'];
    private $settings;

    public function __construct()
    {
        parent::__construct();
        $this->settings = Company::first();
    }

    public function getStartTimeAttribute($value)
    {
        return Carbon::createFromFormat('H:i:s', $value)->setTimezone($this->settings->timezone)->format($this->settings->time_format);
    }

    public function getEndTimeAttribute($value)
    {
        return Carbon::createFromFormat('H:i:s', $value)->setTimezone($this->settings->timezone)->format($this->settings->time_format);
    }

    public function getUtcStartTimeAttribute()
    {
        return Carbon::createFromFormat('H:i:s', $this->attributes['start_time'])->format($this->settings->time_format);
    }

    public function getUtcEndTimeAttribute()
    {
        return Carbon::createFromFormat('H:i:s', $this->attributes['end_time'])->format($this->settings->time_format);
    }

    public function setStartTimeAttribute($value)
    {
        $this->attributes['start_time'] = Carbon::parse($value, $this->settings->timezone)->setTimezone('UTC')->format('H:i:s');
    }

    public function setEndTimeAttribute($value)
    {
        $this->attributes['end_time'] = Carbon::parse($value, $this->settings->timezone)->setTimezone('UTC')->format('H:i:s');
    }

}
