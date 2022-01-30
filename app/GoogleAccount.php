<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\GoogleAccount
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $company_id
 * @property string|null $google_id
 * @property string|null $name
 * @property array|null $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Company|null $company
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAccount whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAccount whereGoogleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAccount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAccount whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAccount whereUserId($value)
 * @mixin \Eloquent
 */
class GoogleAccount extends Model
{
    protected $fillable = [
        'google_id', 'name', 'token',
    ];

    protected $casts = [
        'token' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
