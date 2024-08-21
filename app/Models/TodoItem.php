<?php

namespace App\Models;

use App\Observers\TodoItemObserver;
use App\Scopes\CompanyScope;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TodoItem extends Model
{

    protected static function boot()
    {
        parent::boot();

        static::observe(TodoItemObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

}
