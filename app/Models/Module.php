<?php

namespace App\Models;

use App\Observers\ModuleObserver;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::observe(ModuleObserver::class);
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

}
