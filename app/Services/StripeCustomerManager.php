<?php

namespace App\Services;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function PHPUnit\Framework\isEmpty;

class StripeCustomerManager
{
    private $company;
    private $connect_id;
    private $customer_id;
    private $customer;

    public function __construct()
    {
        $this->company = company();
    }

    public function handleCustomerId(){

        if(!is_null($this->company->stripe_id) && Str::contains($this->company->stripe_id, 'acct_')){
            $this->connect_id = $this->company->stripe_id;
            $this->company->stripe_id = null;
            $this->company->save();
            if(is_null($this->company->customer_id) || empty($this->company->customer_id)){
                $customer = $this->company->createOrGetStripeCustomer();
                $this->company->customer_id = $customer->id;
                $this->company->save();
                $this->restoreConnectId();
                return $customer->id;
            }else{
                $this->company->stripe_id = $this->company->customer_id;
                $this->company->save();
                $this->customer_id = $this->company->customer_id;
                $this->restoreConnectId();
                return $this->customer_id;
            }
        }
        $customer = $this->company->createOrGetStripeCustomer();
        return $customer->id;
    }

    public function getStripeCustomer(){

        if(!is_null($this->company->stripe_id) && Str::contains($this->company->stripe_id, 'acct_')){
            $this->connect_id = $this->company->stripe_id;
            $this->company->stripe_id = null;
            $this->company->save();
            if(is_null($this->company->customer_id) || empty($this->company->customer_id)){
                $customer = $this->company->createOrGetStripeCustomer();

                $this->company->customer_id = $customer->id;
                $this->company->save();
                $this->restoreConnectId();
                return $customer;
            }else{
                $this->company->stripe_id = $this->company->customer_id;
                $this->company->save();
                $this->customer = $this->company->asStripeCustomer();
                $this->restoreConnectId();
                return $this->customer;
            }
        }
        return $this->company->createOrGetStripeCustomer();
    }

    public function restoreConnectId()
    {
        $this->company->stripe_id = $this->connect_id;
        return $this->company->save();
    }
}
