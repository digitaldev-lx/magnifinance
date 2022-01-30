<?php

namespace App;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\ThemeSetting
 *
 * @property int $id
 * @property int|null $company_id
 * @property string $role
 * @property string $primary_color
 * @property string $secondary_color
 * @property string $sidebar_bg_color
 * @property string $sidebar_text_color
 * @property string $topbar_text_color
 * @property string|null $custom_css
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|ThemeSetting newModelQuery()
 * @method static Builder|ThemeSetting newQuery()
 * @method static Builder|ThemeSetting ofAdminRole()
 * @method static Builder|ThemeSetting ofCustomerRole()
 * @method static Builder|ThemeSetting ofSuperAdminRole()
 * @method static Builder|ThemeSetting query()
 * @method static Builder|ThemeSetting whereCompanyId($value)
 * @method static Builder|ThemeSetting whereCreatedAt($value)
 * @method static Builder|ThemeSetting whereCustomCss($value)
 * @method static Builder|ThemeSetting whereId($value)
 * @method static Builder|ThemeSetting wherePrimaryColor($value)
 * @method static Builder|ThemeSetting whereRole($value)
 * @method static Builder|ThemeSetting whereSecondaryColor($value)
 * @method static Builder|ThemeSetting whereSidebarBgColor($value)
 * @method static Builder|ThemeSetting whereSidebarTextColor($value)
 * @method static Builder|ThemeSetting whereTopbarTextColor($value)
 * @method static Builder|ThemeSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ThemeSetting extends Model
{
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function scopeOfSuperAdminRole($query)
    {
        return $query->whereRole('superadmin');
    }

    public function scopeOfAdminRole($query)
    {
        return $query->whereRole('administrator');
    }

    public function scopeOfCustomerRole($query)
    {
        return $query->whereRole('customer');
    }

}
