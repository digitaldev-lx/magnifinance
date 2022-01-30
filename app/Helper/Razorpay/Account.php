<?php

namespace App\Helper\Razorpay;

use Razorpay\Api\Entity;

class Account extends Entity
{

    public function create($attributes = array())
    {
        $entityUrl = 'beta/accounts';

        return $this->request('POST', $entityUrl, $attributes);
    }

    public function fetch($id)
    {
        $entityUrl = 'beta/accounts/';

        $this->validateIdPresence($id);

        $relativeUrl = $entityUrl . $id;

        return $this->request('GET', $relativeUrl);
    }

    public function all($options = array())
    {
        $entityUrl = 'beta/accounts';

        return $this->request('GET', $entityUrl, $options);
    }

}
