<?php

namespace DigitalDevLX\Magnifinance\traits;

use DigitalDevLX\Magnifinance\models\Document;

trait Documentable
{
    public function document()
    {
        return $this->morphOne(Document::class, 'documentable');
    }

    public function addDocument($document_id)
    {
        return $this->document()->create([
            'document_id' => $document_id
        ]);
    }

}
