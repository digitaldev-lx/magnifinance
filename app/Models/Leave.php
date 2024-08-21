<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{

    public function employee()
    {
        return $this->belongsTo(User::class);
    }

}
