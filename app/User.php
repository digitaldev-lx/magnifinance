<?php

namespace App;

use App\Observers\UserObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laratrust\Traits\LaratrustUserTrait;
use Laravel\Cashier\Billable;

/**
 * App\User
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $group_id
 * @property string $name
 * @property string $email
 * @property string|null $calling_code
 * @property string|null $mobile
 * @property int $mobile_verified
 * @property string $password
 * @property string|null $image
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $country_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Booking[] $bookings
 * @property-read int|null $bookings_count
 * @property-read \App\Company|null $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Booking[] $completedBookings
 * @property-read int|null $completed_bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Booking[] $customerBookings
 * @property-read int|null $customer_bookings_count
 * @property-read \App\EmployeeGroup|null $employeeGroup
 * @property-read mixed $formatted_mobile
 * @property-read mixed $is_admin
 * @property-read mixed $is_agent
 * @property-read mixed $is_customer
 * @property-read mixed $is_employee
 * @property-read mixed $is_superadmin
 * @property-read mixed $is_superadmin_employee
 * @property-read mixed $mobile_with_code
 * @property-read mixed $modules
 * @property-read mixed $role
 * @property-read mixed $user_image_url
 * @property-read \App\GoogleAccount|null $googleAccount
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Leave[] $leave
 * @property-read int|null $leave_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BusinessService[] $services
 * @property-read int|null $services_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TodoItem[] $todoItems
 * @property-read int|null $todo_items_count
 * @method static Builder|User allAdministrators()
 * @method static Builder|User allAgents()
 * @method static Builder|User allCustomers()
 * @method static Builder|User allEmployees()
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User orWherePermissionIs($permission = '')
 * @method static Builder|User orWhereRoleIs($role = '', $team = null)
 * @method static Builder|User otherThanCustomers()
 * @method static Builder|User query()
 * @method static Builder|User whereCallingCode($value)
 * @method static Builder|User whereCompanyId($value)
 * @method static Builder|User whereCountryId($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereGroupId($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereImage($value)
 * @method static Builder|User whereMobile($value)
 * @method static Builder|User whereMobileVerified($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePermissionIs($permission = '', $boolean = 'and')
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereRoleIs($role = '', $team = null, $boolean = 'and')
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use LaratrustUserTrait, Notifiable;

    protected static function boot()
    {
        parent::boot();

        static::observe(UserObserver::class);

        $company = company();

        $role = Role::withoutGlobalScopes()->select('name')->get();

        foreach($role as $roles){
            if($roles->name != 'customer') {
                static::addGlobalScope(new CompanyScope);
            }
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','calling_code', 'mobile', 'password', 'company_id', 'vat_number', 'address', 'city', 'post_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = [
        'user_image_url', 'mobile_with_code', 'formatted_mobile'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function getUserImageUrlAttribute()
    {
        if (is_null($this->image)) {
            return "https://media.istockphoto.com/vectors/profile-picture-vector-illustration-vector-id587805156?k=20&m=587805156&s=612x612&w=0&h=Ok_jDFC5J1NgH20plEgbQZ46XheiAF8sVUKPvocne6Y=";
//            return cdn_storage_url("images/default-avatar-user.png");
        }
        return cdn_storage_url($this->image);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function completedBookings()
    {
        return $this->hasMany(Booking::class, 'user_id')->where('bookings.status', 'completed');
    }

    public function employeeGroup()
    {
        return $this->belongsTo(EmployeeGroup::class, 'group_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function leave()
    {
        return $this->hasMany('App\Leave', 'employee_id', 'id');
    }

    public function todoItems()
    {
        return $this->hasMany(TodoItem::class);
    }

    public function getRoleAttribute()
    {
        return $this->roles->first();
    }

    public function getMobileWithCodeAttribute()
    {
        return substr($this->calling_code, 1).$this->mobile;
    }

    public function getFormattedMobileAttribute()
    {
        if (!$this->calling_code) {
            return $this->mobile;
        }

        return $this->calling_code.'-'.$this->mobile;
    }

    // @codingStandardsIgnoreLine
    public function routeNotificationForNexmo($notification)
    {
        return $this->mobile_with_code;
    }

    // @codingStandardsIgnoreLine
    public function routeNotificationForMsg91($notification)
    {
        return $this->mobile_with_code;
    }

    public function googleAccount()
    {
        return $this->hasOne(GoogleAccount::class);
    }

    public function getIsSuperadminAttribute()
    {
        return $this->hasRole('superadmin');
    }

    public function getIsSuperadminEmployeeAttribute()
    {
        if (($this->company_id == null && !$this->hasRole('customer')) || $this->is_superadmin) {
            return true;
        }

        return false;
    }

    public function getIsAgentAttribute()
    {
        return $this->hasRole('agent');
    }

    public function getIsAdminAttribute()
    {
        return $this->hasRole('administrator');
    }

    public function getIsEmployeeAttribute()
    {
        return $this->hasRole('employee');
    }

    public function getrIsCustomeAttribute()
    {
        if ($this->roles()->withoutGlobalScopes()->where('roles.name', 'customer')->count() > 0) {
            return true;
        }

        return false;
    }

    public function scopeAllAgents()
    {
        return $this->whereHas('roles', function ($query) {
            $query->withoutGlobalScopes()->where('name', 'agent');
        });
    }

    public function scopeAllAdministrators()
    {
        return $this->whereHas('roles', function ($query) {
            $query->withoutGlobalScopes()->where('name', 'administrator');
        });
    }

    public function scopeAllSuperAdmins()
    {
        return $this->whereHas('roles', function ($query) {
            $query->whereIn('name', ['superadmin']);
        });
    }

    public function scopeAllCustomers()
    {
        return $this->whereHas('roles', function ($query) {
            $query->where('name', 'customer')->withoutGlobalScopes();
        });
    }

    public function scopeNotCustomer()
    {
        return $this->whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['customer'])->withoutGlobalScopes();
        });
    }

    public function scopeOtherThanCustomers()
    {
        return $this->whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['superadmin', 'customer']);
        });
    }

    public function scopeAllEmployees()
    {
        return $this->whereHas('roles', function ($query) {
            $query->where('name', 'employee');
        });
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class);
    }

    public function customerBookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function services()
    {
        return $this->belongsToMany(BusinessService::class);
    }

    public function userBookingCount($date)
    {
        return Booking::where('user_id', $this->id)->whereDate('created_at', $date)->count();
    }

    public function getModulesAttribute()
    {
        return ModuleSetting::select('module_name')->where(['status' => 'active', 'type' => $this->role->name])->get()->map(function ($item, $key) { return $item->module_name;
        })->toArray();
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('mobile', 'like', '%' . $search . '%');
        });
    }

}
