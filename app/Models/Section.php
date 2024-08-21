<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    public $timestamps = false;

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

}
