<?php

namespace App\Traits;


use App\Models\Document;
use App\Models\PaymentGatewayCredentials;
use Carbon\Carbon;
use DigitalDevLX\Magnifinance\facades\Magnifinance;

trait Documentable
{

    public function document()
    {
        return $this->morphOne(Document::class, "documentable");
    }

    private function addDocument(string $document_id)
    {
        return $this->document()->updateOrCreate([
            "document_id" => $document_id
        ]);
    }

    public function getDocument(){
        if($this->getMorphClass() == "App\Booking"){
            $document = Magnifinance::getDocumentFromPartner($this->document->document_id, $this->company->vat_number);
        }else{
            $document = Magnifinance::getDocumentFromOwner($this->document->document_id);
        }

        return response()->streamDownload(function () use($document){
            echo file_get_contents($document->Object->DownloadUrl);
        }, $this->document->document_id.".pdf");
    }



    public function emitDocument($company = null)
    {
        switch ($this->getMorphClass()){
            case ("App\Booking"):
                $document = Magnifinance::emitDocumentFromPartner($this->company->vat_number, $this->user, $this->getDocumentData(), $this->company->company_email);
                if($document->IsSuccess){
                    return response()->json(["success" => $document->IsSuccess, "document" => $this->addDocument($document->Object->DocumentId)]);
                }else{
                    return response()->json(["success" => $document->IsSuccess, "message" => $document], 403);
                }
                break;
            case ("App\Tout"):
            case ("App\Package"):

                $company = is_null($company) ? $this->company : $company;
                $document = Magnifinance::emitDocumentFromOwner($company, $this->getDocumentData($company), $this->company->company_email);
                if($document->IsSuccess){
                    return response()->json(["success" => $document->IsSuccess, "document" => $this->addDocument($document->Object->DocumentId)]);
                }else{
                    return response()->json(["success" => $document->IsSuccess, "message" => $document], 403);
                }
                break;
        }

    }

    private function getDocumentData($company = null)
    {

        if($this->getMorphClass() == "App\Booking"){
            $data = [
                "Type" => "T", // T = Fatura/Recibo, I = Fatura, S = Fatura Simplificada, C - Nota de Credito, D = Nota de Debito
                "Date" => $this->payment->paid_on->format("Y-m-d"), // Data do Serviço format("Y-m-d")
                "DueDate" => $this->payment->paid_on->format("Y-m-d"), // Data do Pagamento
                "Description" => get_class($this),
                "Currency" => $this->company->currency->currency_code,
                "RetentionPercentage" => PaymentGatewayCredentials::first()->stripe_commission_percentage,
//            "Serie" => "",
//            "TaxExemptionReasonCode" => "",
                "ExternalId" => $this->payment->transaction_id, //transaction Id
                "Lines" => $this->generateItemsList($this->items)
            ];
        }elseif ($this->getMorphClass() == "App\Tout"){
            $data = [
                "Type" => "T", // T = Fatura/Recibo, I = Fatura, S = Fatura Simplificada, C - Nota de Credito, D = Nota de Debito
                "Date" => $this->paid_on->format("Y-m-d"), // Data do Serviço format("Y-m-d")
                "DueDate" => $this->paid_on->format("Y-m-d"), // Data do Pagamento
                "Description" => get_class($this),
                "Currency" => $this->company->currency->currency_code,
                "RetentionPercentage" => PaymentGatewayCredentials::first()->stripe_commission_percentage,
//            "Serie" => "",
//            "TaxExemptionReasonCode" => "",
                "ExternalId" => $this->transaction_id, //transaction Id
                "Lines" => $this->generateItem()
            ];
        }else{

            $data = [
                "Type" => "T", // T = Fatura/Recibo, I = Fatura, S = Fatura Simplificada, C - Nota de Credito, D = Nota de Debito
                "Date" => Carbon::now()->format("Y-m-d"), // Data do Serviço format("Y-m-d")
                "DueDate" => Carbon::now()->format("Y-m-d"), // Data do Pagamento
                "Description" => "Subscription " . $this->name,
                "Currency" => $company->currency->currency_code,
                "RetentionPercentage" => PaymentGatewayCredentials::first()->stripe_commission_percentage,
//            "Serie" => "",
//            "TaxExemptionReasonCode" => "",
                "ExternalId" => $this->transaction_id, //transaction Id
                "Lines" => $this->generateItem($company)
            ];
        }


        if($this->document){
            $data["id"] = $this->document->document_id;
        }

        return $data;
    }

    private function generateItemsList($list){
        $array = [];

        foreach ($list as $item){
            $len = max(strlen($item->id), 3);
            $array[] = [
                "Code" => str_pad($item->id, $len, "0", STR_PAD_LEFT), // Service or Product ID, min lenght 2
                "Description" => $this->name . " Plan",
                "UnitPrice" => round($item->businessService->net_price / (1 + $item->businessService->taxServices[0]->tax->percent / 100), 2, PHP_ROUND_HALF_UP),
                "Quantity" => $item->quantity,
                "Unit" => "Service",
                "Type" => "S", // S = Service P = Product
                "TaxValue" => $item->businessService->taxServices[0]->tax->percent, // percentage
                "ProductDiscount" => 0, // Percentage
                "CostCenter" => get_class($item)
            ];
        }

        return $array;
    }

    private function generateItem($company = null){
        $package = $company->package_type."_price";
        $array = [];

            $len = max(strlen($this->id), 3);
            $array[] = [
                "Code" => str_pad($this->id, $len, "0", STR_PAD_LEFT), // Service or Product ID, min lenght 2
                "Description" => get_class($this) ." ".$this->from ." - ". $this->to,
                "UnitPrice" => round(round($this->$package) / (1 + 23 / 100), 2, PHP_ROUND_HALF_UP),
                "Quantity" => 1,
                "Unit" => "Service",
                "Type" => "S", // S = Service P = Product
                "TaxValue" => 23, // percentage
                "ProductDiscount" => 0, // Percentage
                "CostCenter" => get_class($this)
            ];

        return $array;
    }
}



