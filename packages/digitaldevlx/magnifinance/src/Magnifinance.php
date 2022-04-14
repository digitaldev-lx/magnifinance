<?php

namespace DigitalDevLX\Magnifinance;

use App\Company;
use App\User;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class Magnifinance
{
    private $base_url;
    private $token;
    private $user;

    public function __construct($owner = [], $client = [], $partner = [])
    {
        $this->base_url = config('magnifinance.MAGNIFINANCE_BASE_URL');
        $this->token = config('magnifinance.MAGNIFINANCE_TOKEN');
        $this->user = config('magnifinance.MAGNIFINANCE_USER');
    }


    /**
     * Add a new partner.
     *
     * @param Company $company
     * @return mixed
     */
    public function addPartner($company)
    {
        return json_decode($this->postRequest($this->generatePartner($company),"partner"));
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
     * @return mixed
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
     * @param User $client
     * @param array $document
     * @param string $sendToEmail
     * @return mixed
     */
    public function emitDocumentFromPartner($partnerNif, User $client, array $document, string $sendToEmail)
    {
        $partnerToken = $this->getPartnerToken($partnerNif);

        $data = [];
        $dataDocument = [];

        /*foreach ($client as $key => $value){
            $dataClient[$key] = $value;
        }*/

        foreach ($document as $key => $value){
            $dataDocument[$key] = $value;
        }

        $data['Client'] = $this->generateClient($client);
        $data['Document'] = $dataDocument;
        $data['IsToClose'] = true;
        $data['SendTo'] = $sendToEmail;
        $response = $this->postRequest($data, 'document', $partnerToken);
        return json_decode($response);
    }

    /**
     * Emit a document from owner.
     *
     * @param $object
     * @param array $document
     * @param string $sendToEmail
     * @return mixed
     */
    public function emitDocumentFromOwner($company, array $document, string $sendToEmail)
    {
        $data = [];
        $dataClient = $this->generateCompanyAsClient($company);
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
    public function getPartnerToken($nif): string
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
     * @return PromiseInterface|Response
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
     * @return PromiseInterface|Response
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
            $headers['password'] = config("magnifinance.MAGNIFINANCE_PASSWORD");
        }

        return Http::withHeaders($headers)->get($url);
    }

    /**
     * @param $company
     * @return array
     */
    private function generatePartner($company): array
    {
        return [
            "UserName" => $company->owner->name,
            "UserEmail" => $company->owner->email,
            "UserPhone" => !is_null($company->owner->mobile) ? $company->owner->calling_code.$company->owner->mobile : $company->company_phone,
            "CompanyTaxId" => $company->vat_number,
            "CompanyLegalName" => $company->company_name,
            "CompanyAddress" => $company->address,
            "CompanyCity" => $company->city,
            "CompanyPostCode" => $company->country->iso == "PT" ? $company->post_code : "1000-001",
            "CompanyCountry" => $company->country->iso
        ];

    }

    /**
     * @param $client
     * @return array
     */
    private function generateClient($client): array
    {
        return $this->clientResponse($client);
    }

    /**
     * @param $company
     * @return array
     */
    private function generateCompanyAsClient($company): array
    {
        return $this->clientResponse($company);
    }

    /**
     * @param $obj
     * @return array
     */
    private function clientResponse($obj): array
    {
        $isCompany = get_class($obj) == "App\Company";
        return [
            "Name" => $isCompany ? $obj->owner->name : $obj->name,
            "NIF" => $obj->vat_number,
            "Email" => $isCompany ? $obj->company_email : $obj->email,
            "Address" => $obj->address == "" ?? $obj->address,
            "City" => $obj->city == "" ?? $obj->city,
            "PostCode" => $obj->country->iso == "PT"
                ? is_null($obj->post_code) || $obj->post_code == ""
                    ? "0000-00"
                    : $obj->post_code
                : "1000-001",
            "CountryCode" => $obj->country->iso,
            "LegalName" => $isCompany ? $obj->company_name: $obj->name,
            "PhoneNumber" => $isCompany ? $obj->company_phone : $obj->mobile
        ];
    }
}
