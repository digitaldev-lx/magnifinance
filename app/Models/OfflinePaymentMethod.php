<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfflinePaymentMethod extends Model
{
    protected $table = 'offline_payment_methods';
    protected $dates = ['created_at'];

    protected $guarded = ['id'];

    public static function activeMethod()
    {
        return OfflinePaymentMethod::where('status', 'yes')->get();
    }

}
