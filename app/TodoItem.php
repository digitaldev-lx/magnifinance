<?php

namespace App;

use App\Scopes\CompanyScope;
use App\Observers\TodoItemObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\TodoItem
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $company_id
 * @property string $title
 * @property string $status
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $user
 * @method static Builder|TodoItem newModelQuery()
 * @method static Builder|TodoItem newQuery()
 * @method static Builder|TodoItem query()
 * @method static Builder|TodoItem status($status)
 * @method static Builder|TodoItem whereCompanyId($value)
 * @method static Builder|TodoItem whereCreatedAt($value)
 * @method static Builder|TodoItem whereId($value)
 * @method static Builder|TodoItem wherePosition($value)
 * @method static Builder|TodoItem whereStatus($value)
 * @method static Builder|TodoItem whereTitle($value)
 * @method static Builder|TodoItem whereUpdatedAt($value)
 * @method static Builder|TodoItem whereUserId($value)
 * @mixin \Eloquent
 */
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
