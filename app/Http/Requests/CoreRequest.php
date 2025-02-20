<?php

namespace App\Http\Requests;

use App\Helper\Reply;
use App\Models\Company;
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
