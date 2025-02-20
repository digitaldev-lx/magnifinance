<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Controllers\SuperAdminBaseController;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Tout;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

;

class ShowDashboard extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.dashboard'));
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if(\request()->ajax())
        {

            $startDate = Carbon::createFromFormat($this->settings->date_format, $request->startDate)->format('Y-m-d');
            $endDate = Carbon::createFromFormat($this->settings->date_format, $request->endDate)->format('Y-m-d');

            $totalCustomers = User::withoutGlobalScopes()->allCustomers()
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->count();
            $totalVendors = User::withoutGlobalScopes()->allAdministrators()
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->count();

            $totalEarnings = Booking::withoutGlobalScopes()->whereDate('date_time', '>=', $startDate)
                ->whereDate('date_time', '<=', $endDate)
                ->where('payment_status', 'completed')
                ->sum('amount_to_pay');

            $totalToutes = Tout::withoutGlobalScopes()->whereDate('paid_on', '>=', $startDate)
                ->whereDate('paid_on', '<=', $endDate)
                ->where('status', 'completed')
                ->sum('amount');

            $activeCompanies = Company::where('status', '=', 'active')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->count();
            $deActiveCompanies = Company::where('status', '=', 'inactive')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->count();
            // return $totalCustomers;
            return Reply::dataOnly(['status' => 'success', 'totalCustomers' => $totalCustomers, 'totalToutes' => $totalToutes, 'totalEarnings' => round($totalEarnings, 2), 'totalVendors' => $totalVendors, 'activeCompanies' => $activeCompanies, 'deActiveCompanies' => $deActiveCompanies,]);
        }


        $this->totalCategories = Category::withoutGlobalScopes()->count();
        $this->todoItemsView = $this->generateTodoView();
        $this->isNotSetExchangeRate = (Currency::where('exchange_rate', null)->where('deleted_at', null)->count() > 0);

        return view('superadmin.dashboard.index', $this->data);
    }

}
