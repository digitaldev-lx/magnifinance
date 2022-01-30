<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Ticket
 *
 * @property-read \App\User $agent
 * @property-read \App\TicketPriority $priority
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TicketComment[] $reply
 * @property-read int|null $reply_count
 * @property-read \App\TicketType $type
 * @property-read \App\Company $vendor
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket agent($agent_id)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket newQuery()
 * @method static \Illuminate\Database\Query\Builder|Ticket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket vendor($vendor_id)
 * @method static \Illuminate\Database\Query\Builder|Ticket withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Ticket withoutTrashed()
 * @mixin \Eloquent
 */
class Ticket extends Model
{
    use SoftDeletes;

    public function vendor()
    {
        return $this->belongsTo(Company::class, 'vendor_id')->withoutGlobalScopes();
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id')->withoutGlobalScopes();
    }

    public function type()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function priority()
    {
        return $this->belongsTo(TicketPriority::class);
    }

    public function reply()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function scopeAgent($query, $agent_id)
    {
        return $query->where('agent_id', $agent_id);
    }

    public function scopeVendor($query, $vendor_id)
    {
        return $query->where('vendor_id', $vendor_id);
    }

}
