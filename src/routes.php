<?php

use DigitalDevLX\Magnifinance\facades\MagnifinanceFacade as Magnifinance;

Route::get('magnifinance', function(){
    return $data = [
        "UserName" => 'teste',
        "UserEmail" => 'teste',
        "UserPhone" => 'teste',
        "CompanyTaxId" => 'teste',
        "CompanyAddress" => 'teste',
        "CompanyCity" => 'teste',
        "CompanyPostCode" => 'teste',
        "CompanyCountry" => 'teste',
    ];
    Magnifinance::addPartner($data);
});