<?php

namespace DigitalDevLX\Magnifinance;

use Illuminate\Support\Facades\Http;

class Magnifinance
{
    private $base_url;
    private $token;
    private $user;

    public function __construct()
    {
        $this->base_url = env('MAGNIFINANCE_BASE_URL');
        $this->token = env('MAGNIFINANCE_TOKEN');
        $this->user = env('MAGNIFINANCE_USER');
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

    public function getDocument($id, $partnerNif)
    {
        $partnerToken = $this->getPartnerToken($partnerNif);
        $document = $this->getRequest('document', null, 'documentId', $id, false, $partnerToken);
        return json_decode($document);
    }

    public function addDocument($partnerNif, $client, $document, $sendTo)
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
        $data['SentTo'] = $sendTo;

        return $this->postRequest($data, 'document', $partnerToken);
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
}
