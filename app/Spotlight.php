<?php

namespace App;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Spotlight
 *
 * @property int $id
 * @property int $company_id
 * @property int $deal_id
 * @property string $from_date
 * @property string $to_date
 * @property string $sequence
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Company $company
 * @property-read \App\Deal $deal
 * @method static \Illuminate\Database\Eloquent\Builder|Spotlight activeCompany()
 * @method static \Illuminate\Database\Eloquent\Builder|Spotlight newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Spotlight newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Spotlight query()
 * @method static \Illuminate\Database\Eloquent\Builder|Spotlight whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spotlight whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spotlight whereDealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spotlight whereFromDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spotlight whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spotlight whereSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spotlight whereToDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spotlight whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Spotlight extends Model
{
    protected $table = 'spotlight';

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function scopeActiveCompany($query)
    {
        return $query->whereHas('company', function($q){
            $q->withoutGlobalScope(CompanyScope::class)->active();
        });
    }

}
