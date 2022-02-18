<?php

namespace DigitalDevLX\Magnifinance;

use App\GlobalSetting;
use Illuminate\Support\Facades\Http;

class Magnifinance
{
    private $base_url;
    private $token;
    private $user;
    private $owner;
    private $partner;
    private $client;

    public function __construct($owner = [], $client = [], $partner = [])
    {
        $this->base_url = env('MAGNIFINANCE_BASE_URL');
        $this->token = env('MAGNIFINANCE_TOKEN');
        $this->user = env('MAGNIFINANCE_USER');

        $this->owner = $this->generateOwner();
        $this->client = $client;
        $this->partner = $partner;
    }

    public function addPartner($partner = [])
    {
        $data = [];
        foreach ($partner as $key => $value){
            $data[$key] = $value;
        }
        return $this->postRequest($data, "partner");
    }

    public function getPartnerToken($nif): string
    {
        $response = $this->getRequest('partner', 'accessTokens', 'partnerTaxId' , $nif, true);
        $token = json_decode($response);
        $tokenObject = (array) $token;
        $token = (array) $tokenObject['Object'][0];
        return $token['AccessToken'];
    }

    public function getDocumentFromPartner($id, $partnerNif)
    {
        $partnerToken = $this->getPartnerToken($partnerNif);
        $document = $this->getRequest('document', null, 'documentId', $id, false, $partnerToken);
        return json_decode($document);
    }

    public function getDocumentFromOwner($id)
    {
        $document = $this->getRequest('document', null, 'documentId', $id, false);
        return json_decode($document);
    }

    public function emitDocumentFromPartner($partnerNif, $client, $document, $sendTo)
    {
        $partnerToken = $this->getPartnerToken($partnerNif);

        $data = [];
        $dataClient = [];
        $dataDocument = [];

        foreach ($client as $key => $value){
            $dataClient[$key] = $value;
        }

        foreach ($document as $key => $value){
            $dataDocument[$key] = $value;
        }

        $data['Client'] = $dataClient;
        $data['Document'] = $dataDocument;
        $data['IsToClose'] = true;
        $data['SendTo'] = $sendTo;

        $response = $this->postRequest($data, 'document', $partnerToken);
        return json_decode($response);
    }

    public function emitDocumentFromOwner($client, $document, $sendTo)
    {

        $data = [];
        $dataClient = [];
        $dataDocument = [];

        foreach ($client as $key => $value){
            $dataClient[$key] = $value;
        }

        foreach ($document as $key => $value){
            $dataDocument[$key] = $value;
        }

        $data['Client'] = $dataClient;
        $data['Document'] = $dataDocument;
        $data['IsToClose'] = true;
        $data['SendTo'] = $sendTo;
        $response = $this->postRequest($data, 'document');
        return json_decode($response);
    }

    private function postRequest($data, $endpoint = "", $partnerToken = null)
    {
        $url = $this->base_url.$endpoint;
        $headers = [
            'email' => $this->user,
            'token' => is_null($partnerToken) ? $this->token : $partnerToken
        ];
        return Http::withHeaders($headers)->post($url, $data);
    }

    private function getRequest($endpoint, $method, $param, $value, $withPassword = false, $partnerToken = null)
    {
        $url = !is_null($method)
            ? $this->base_url.$endpoint."/".$method."?".$param."=".$value
            : $this->base_url.$endpoint."?".$param."=".$value;

        $headers = [
            'email' => $this->user,
            'token' => is_null($partnerToken) ? $this->token : $partnerToken
        ];

        if($withPassword){
            $headers['password'] = env('MAGNIFINANCE_PASSWORD');
        }

        return Http::withHeaders($headers)->get($url);
    }

    private function generatePartner()
    {
        $company = company()->load('owner');

        return [
            "UserName" => $company->owner->name,
            "UserEmail" => $company->owner->email,
            "UserPhone" => $company->owner->calling_code.$company->owner->mobile,
            "CompanyTaxId" => $company->vat_number,
            "CompanyLegalName" => $company->company_name,
            "CompanyAddress" => $company->address,
            "CompanyCity" => $company->city,
            "CompanyPostCode" => $company->post_code,
            "CompanyCountry" => $company->country
        ];

        // todo: create migration to add vat_number and post_code to company
    }

    private function generateClient()
    {
        $client = auth()->user()->load('country');

        return [
            "Name" => $client->name,
            "NIF" => $client->vat_number,
            "Email" => $client->email,
            "Address" => "",
            "City" => "",
            "PostCode" => "",
            "CountryCode" => $client->country->iso,
            "LegalName" => $client->name,
            "PhoneNumber" => $client->calling_code.$client->mobile
        ];

        // todo: create migration to add address, city, post_code and vat_number to user
        // todo: create migration to add vat_number to global settings
    }
}
