<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;

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
