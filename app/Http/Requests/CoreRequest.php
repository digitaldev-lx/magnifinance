<?php

namespace App\Http\Requests;

use App\Company;
use App\Helper\Reply;
use Illuminate\Foundation\Http\FormRequest;

class CoreRequest extends FormRequest
{

    public function __construct()
    {
        parent::__construct();
        $this->settings = Company::first();
    }

    protected function formatErrors(\Illuminate\Contracts\Validation\Validator  $validator)
    {
        return Reply::formErrors($validator);
    }

}
