<?php

namespace App\Rules;

use App\Models\BusinessService;
use Illuminate\Contracts\Validation\Rule;

class BusinessServiceUniqueSlug implements Rule
{

    private $company_id;
    private $service_slug;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($company_id, $service_slug)
    {
        $this->company_id = $company_id;
        $this->service_slug = $service_slug;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !BusinessService::whereCompanyId($this->company_id)->where('slug', $this->service_slug)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Your Slug already exists in your Business.';
    }
}
