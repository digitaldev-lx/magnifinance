<?php

namespace DigitalDevLX\Magnifinance;

use App\Company;
use App\GlobalSetting;
use Illuminate\Support\Facades\Http;

class Magnifinance
{
    private $base_url;
    private $token;
    private $user;

    public function __construct($owner = [], $client = [], $partner = [])
    {
        $this->base_url = env('MAGNIFINANCE_BASE_URL');
        $this->token = env('MAGNIFINANCE_TOKEN');
        $this->user = env('MAGNIFINANCE_USER');
    }


    /**
     * Add a new partner.
     *
     * @param Company $company
     * @return array
     */
    public function addPartner(Company $company)
    {
        return json_decode($this->postRequest($this->generatePartner($company), "partner"));
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
        $data['SendTo'] = $sendToEmail;

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
    public function emitDocumentFromOwner($object, array $document, string $sendToEmail)
    {
        $company = $object->load('company')->company;
        $data = [];
        return $dataClient = $this->generateCompanyAsClient($company);
        $dataDocument = [];

        foreach ($document as $key => $value){
            $dataDocument[$key] = $value;
        }

        $data['Client'] = $dataClient;
        $data['Document'] = $dataDocument;
        $data['IsToClose'] = true;
        $data['SendTo'] = $dataClient["Email"];
        $response = $this->postRequest($data, 'document');
        return json_decode($response);
    }

    /**
     * Get partner token.
     *
     * @param $nif
     * @return string
     */
    public function getPartnerToken($nif)
    {
        $company = company();
        if($company->partner_token !== "" || is_null($company->partner_token)){
            return $company->partner_token;
        }
        $response = $this->getRequest('partner', 'accessTokens', 'partnerTaxId' , $nif, true);
        $token = json_decode($response);
        $tokenObject = (array) $token;
        $token = (array) $tokenObject['Object'][0];
        $company->partner_token = $token['AccessToken'];
        $company->save();
        return $token['AccessToken'];
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
    private function getRequest(string $endpoint, $method, string $param, $value, bool $withPassword = false, $partnerToken = null)
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

    private function generatePartner($company)
    {
        $company->load(['owner', 'country']);

        return [
            "UserName" => $company->owner->name,
            "UserEmail" => $company->owner->email,
            "UserPhone" => $company->owner->calling_code.$company->owner->mobile,
            "CompanyTaxId" => $company->vat_number,
            "CompanyLegalName" => $company->company_name,
            "CompanyAddress" => $company->address,
            "CompanyCity" => $company->city,
            "CompanyPostCode" => $company->country->iso == "PT" ? $company->post_code : "0000-000",
            "CompanyCountry" => $company->country->iso
        ];

    }

    private function generateClient()
    {
        return $this->clientResponse(auth()->user()->load('country'));
    }

    private function generateCompanyAsClient($company)
    {
        return $this->clientResponse($company->load('country'));
    }

    private function clientResponse($company){
        return [
            "Name" => $company->owner->name,
            "NIF" => $company->vat_number,
            "Email" => $company->company_email,
            "Address" => $company->address == "" ?? $company->address,
            "City" => $company->city == "" ?? $company->city,
            "PostCode" => $company->country->iso == "PT"
                ? is_null($company->post_code) || $company->post_code == ""
                    ? "0000-00"
                    : $company->post_code
                : "0000-000",
            "CountryCode" => $company->country->iso,
            "LegalName" => $company->company_name,
            "PhoneNumber" => $company->company_phone
        ];
    }
}
