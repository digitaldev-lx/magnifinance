<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ["documentable_id", "documentable_type", "document_id"];

    public function documentable()
    {
        return $this->morphTo();
    }
}
