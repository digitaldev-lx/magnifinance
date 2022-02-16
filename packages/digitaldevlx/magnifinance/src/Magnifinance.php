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

    private function postRequest($data, $endpoint = "")
    {
        $url = $this->base_url.$endpoint;
        return Http::withHeaders([
            'email' => $this->user,
            'token' => $this->token
        ])->post($url, $data);
    }
}
