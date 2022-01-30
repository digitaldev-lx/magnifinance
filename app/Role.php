<?php

namespace App;

use App\Scopes\CompanyScope;
use App\Observers\RoleObserver;
use Laratrust\Models\LaratrustRole;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Role
 *
 * @property int $id
 * @property int|null $company_id
 * @property string $name
 * @property string|null $display_name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $member_count
 * @property-read mixed $users
 * @property-read mixed $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @method static Builder|Role users()
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static Builder|Role query()
 * @method static Builder|Role whereCompanyId($value)
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereDescription($value)
 * @method static Builder|Role whereDisplayName($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Role extends LaratrustRole
{
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::observe(RoleObserver::class);

        static::addGlobalScope('withoutCustomerRole', function (Builder $builder) {
            if (company()) {
                $builder->whereNotIn('name', ['customer', 'superadmin', 'agent']);
            }
        });

        static::addGlobalScope(new CompanyScope);
    }

    public function getMemberCountAttribute()
    {
        return $this->users->count();
    }

}
