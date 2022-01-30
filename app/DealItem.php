<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\DealItem
 *
 * @property int $id
 * @property int|null $deal_id
 * @property int|null $business_service_id
 * @property int $quantity
 * @property float $unit_price
 * @property float $discount_amount
 * @property float $total_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\BusinessService|null $businessService
 * @property-read \App\Deal|null $deal
 * @method static \Illuminate\Database\Eloquent\Builder|DealItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DealItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DealItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|DealItem whereBusinessServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealItem whereDealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealItem whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealItem whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DealItem extends Model
{
    // Relations

    public function businessService()
    {
        return $this->belongsTo(BusinessService::class);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

} /* end of class  */
