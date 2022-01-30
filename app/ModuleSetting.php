<?php

namespace App;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\ModuleSetting
 *
 * @property int $id
 * @property int $company_id
 * @property string $module_name
 * @property string $status
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|ModuleSetting newModelQuery()
 * @method static Builder|ModuleSetting newQuery()
 * @method static Builder|ModuleSetting query()
 * @method static Builder|ModuleSetting whereCompanyId($value)
 * @method static Builder|ModuleSetting whereCreatedAt($value)
 * @method static Builder|ModuleSetting whereId($value)
 * @method static Builder|ModuleSetting whereModuleName($value)
 * @method static Builder|ModuleSetting whereStatus($value)
 * @method static Builder|ModuleSetting whereType($value)
 * @method static Builder|ModuleSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ModuleSetting extends Model
{

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    protected $fillable = [ 'company_id', 'module_name', 'status', 'type'];

} /* end of class */
