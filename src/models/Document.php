<?php

namespace DigitalDevLX\Magnifinance\models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $guarded = [];

    /**
     * Get all of the models that own comments.
     */
    public function documentable()
    {
        return $this->morphTo();
    }

}
