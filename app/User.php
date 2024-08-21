<?php

namespace App;

use App\Models\Booking;
use App\Models\BusinessService;
use App\Models\Company;
use App\Models\Country;
use App\Models\EmployeeGroup;
use App\Models\GoogleAccount;
use App\Models\ModuleSetting;
use App\Models\Role;
use App\Models\TodoItem;
use App\Observers\UserObserver;
use App\Scopes\CompanyScope;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;

class User extends Authenticatable implements LaratrustUser
{
    use HasRolesAndPermissions;
    use Notifiable;

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
        return $this->hasMany('App\Models\Leave', 'employee_id', 'id');
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
        return $this->whereRelation('roles', 'name', 'customer')->withoutGlobalScopes();
        /*return $this->whereHas('roles', function ($query) {
            $query->where('name', 'customer')->withoutGlobalScopes();
        });*/
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
