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


    /**
     * Add a new partner.
     *
     * @param  array  $partner
     * @return array
     */
    public function addPartner(array $partner)
    {
        $data = [];
        foreach ($partner as $key => $value){
            $data[$key] = $value;
        }
        $response = $this->postRequest($data, "partner");
        return json_decode($response);
    }

    /**
     * Get partner token.
     *
     * @param $nif
     * @return string
     */
    public function getPartnerToken($nif): string
    {
        $response = $this->getRequest('partner', 'accessTokens', 'partnerTaxId' , $nif, true);
        $token = json_decode($response);
        $tokenObject = (array) $token;
        $token = (array) $tokenObject['Object'][0];
        return $token['AccessToken'];
    }

    /**
     * Get document from partner.
     *
     * @param $id
     * @param $partnerNif
     * @return array
     */
    public function getDocumentFromPartner($id, $partnerNif)
    {
        $partnerToken = $this->getPartnerToken($partnerNif);
        $document = $this->getRequest('document', null, 'documentId', $id, false, $partnerToken);
        return json_decode($document);
    }

    /**
     * Get document from owner.
     *
     * @param $document_id
     * @return array
     */
    public function getDocumentFromOwner($document_id)
    {
        $document = $this->getRequest('document', null, 'documentId', $document_id, false);
        return json_decode($document);
    }

    /**
     * Emit a document from partner.
     *
     * @param $partnerNif
     * @param array $client
     * @param array $document
     * @param string $sendToEmail
     * @return array
     */
    public function emitDocumentFromPartner($partnerNif, array $client, array $document, string $sendToEmail)
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

    /**
     * Emit a document from owner.
     *
     * @param array $client
     * @param array $document
     * @param string $sendToEmail
     * @return array
     */
    public function emitDocumentFromOwner(array $client, array $document, string $sendToEmail)
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

    /**
     * Post request.
     *
     * @param $data
     * @param string $endpoint
     * @param null $partnerToken
     * @return array
     */
    private function postRequest($data, string $endpoint = "", $partnerToken = null)
    {
        $url = $this->base_url.$endpoint;
        $headers = [
            'email' => $this->user,
            'token' => is_null($partnerToken) ? $this->token : $partnerToken
        ];
        return Http::withHeaders($headers)->post($url, $data);
    }

    /**
     * Get request.
     *
     * @param string $endpoint
     * @param string $method
     * @param string $param
     * @param $value
     * @param bool $withPassword
     * @param $partnerToken
     * @return array
     */
    private function getRequest(string $endpoint, string $method, string $param, $value, bool $withPassword = false, $partnerToken = null)
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

    }
}
