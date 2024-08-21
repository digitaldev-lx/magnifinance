<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ItemTax
 *
 * @property int $id
 * @property int|null $tax_id
 * @property int|null $service_id
 * @property int|null $deal_id
 * @property int|null $product_id
 * @property int|null $company_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tax|null $tax
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tax[] $taxes
 * @property-read int|null $taxes_count
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTax newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTax newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTax query()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTax whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTax whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTax whereDealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTax whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTax whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTax whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTax whereTaxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTax whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ItemTax extends Model
{

//    protected $with = ['taxes'];

    public function taxes()
    {
        return $this->belongsToMany(Tax::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

}
