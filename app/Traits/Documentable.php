<?php

namespace App\Traits;


use App\Document;
use DigitalDevLX\Magnifinance\facades\Magnifinance;

trait Documentable
{

    public function document()
    {
        return $this->morphOne(Document::class, "documentable");
    }

    public function addDocument(string $document_id)
    {
        return $this->document()->create([
            "document_id" => $document_id
        ]);
    }

    public function getDocument(){
        $document = Magnifinance::getDocumentFromPartner($this->document->document_id, $this->company->vat_number);

        return response()->streamDownload(function () use($document){
            echo file_get_contents($document->Object->DownloadUrl);
        }, "faturaRecibo.pdf");
    }
}



