<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\SuperAdminBaseController;

class UpdateApplicationController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();

        view()->share('pageTitle', __('menu.updateApp'));
    }

    public function index()
    {
        return view('superadmin.update.index', $this->data);
    }

}
