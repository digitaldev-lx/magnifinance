<?php

namespace App\Models;

use App\Observers\RoleObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Laratrust\Models\Role as RoleModel;

class Role extends RoleModel
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
