<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Booking;
use Carbon\Carbon;
use App\Helper\Reply;
use App\Scopes\CompanyScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AdminBaseController;

class ShowDashboard extends AdminBaseController
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
            $startDate = Carbon::parse($request->startDate)->format('Y-m-d');
            $endDate = Carbon::parse($request->endDate)->format('Y-m-d');

            $totalBooking = Booking::whereDate('date_time', '>=', $startDate)
                ->whereDate('date_time', '<=', $endDate);

            if(!$this->user->is_admin){
                $totalBooking = $totalBooking->where('user_id', $this->user->id);
            }

                $totalBooking = $totalBooking->count();

            $inProgressBooking = Booking::whereDate('date_time', '>=', $startDate)
                ->whereDate('date_time', '<=', $endDate)
                ->where('status', 'in progress');

            if(!$this->user->is_admin){
                $inProgressBooking = $inProgressBooking->where('user_id', $this->user->id);
            }

            $inProgressBooking = $inProgressBooking->count();

            $pendingBooking = Booking::whereDate('date_time', '>=', $startDate)
                ->whereDate('date_time', '<=', $endDate)
                ->where('status', 'pending');

            if(!$this->user->is_admin){
                $pendingBooking = $pendingBooking->where('user_id', $this->user->id);
            }

                $pendingBooking = $pendingBooking->count();

            /*$approvedBooking = Booking::whereDate('date_time', '>=', $startDate)
                ->whereDate('date_time', '<=', $endDate)
                ->where('status', 'approved');*/
            $approvedBooking = Booking::whereBetween('date_time', [$startDate, $endDate])
                ->where('status', 'approved')->get();

            if(!$this->user->is_admin){
                $approvedBooking = $approvedBooking->where('user_id', $this->user->id);
            }

            $approvedBooking = $approvedBooking->count();

            $completedBooking = Booking::whereDate('date_time', '>=', $startDate)
                ->whereDate('date_time', '<=', $endDate)
                ->where('status', 'completed');

            if(!$this->user->is_admin){
                $completedBooking = $completedBooking->where('user_id', $this->user->id);
            }

            $completedBooking = $completedBooking->count();

            $canceledBooking = Booking::whereDate('date_time', '>=', $startDate)
                ->whereDate('date_time', '<=', $endDate)
                ->where('status', 'canceled');

            if(!$this->user->is_admin){
                $canceledBooking = $canceledBooking->where('user_id', $this->user->id);
            }

            $canceledBooking = $canceledBooking->count();

            $offlineBooking = Booking::whereDate('date_time', '>=', $startDate)
                ->whereDate('date_time', '<=', $endDate)
                ->where('source', 'pos');

            if(!$this->user->is_admin){
                $offlineBooking = $offlineBooking->where('user_id', $this->user->id);
            }

            $offlineBooking = $offlineBooking->count();

            $onlineBooking = Booking::whereDate('date_time', '>=', $startDate)
                ->whereDate('date_time', '<=', $endDate)
                ->where('source', 'online');

            if(!$this->user->is_admin){
                $onlineBooking = $onlineBooking->where('user_id', $this->user->id);
            }

            $assignedPendingBooking = Booking::whereDate('date_time', '>=', $startDate)
                ->whereDate('date_time', '<=', $endDate)
                ->where('status', 'pending');

                $assignedPendingBooking = $assignedPendingBooking->count();


            if($this->user->is_admin){
                $totalCustomers = User::allCustomers()
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->count();

                $totalEarnings = Booking::whereBetween('date_time', [$startDate, $endDate])
                    ->where('payment_status', 'completed')
                    ->sum('amount_to_pay');

                /*$totalEarnings = Booking::whereDate('date_time', '>=', $startDate)
                    ->whereDate('date_time', '<=', $endDate)
                    ->where('payment_status', 'completed')
                    ->sum('amount_to_pay');*/
            }
            else{
                $totalCustomers = 0;
                $totalEarnings = 0;
            }

            return Reply::dataOnly(['status' => 'success', 'totalBooking' => $totalBooking, 'assignedPendingBooking' => $assignedPendingBooking, 'pendingBooking' => $pendingBooking, 'approvedBooking' => $approvedBooking, 'inProgressBooking' => $inProgressBooking, 'completedBooking' => $completedBooking, 'canceledBooking' => $canceledBooking, 'offlineBooking' => $offlineBooking, 'onlineBooking' => $onlineBooking, 'totalCustomers' => $totalCustomers, 'totalEarnings' => round($totalEarnings, 2), 'user' => $this->user]);
        }

        if($this->user->is_admin){
            $recentSales = Booking::orderBy('id', 'desc')
                ->with(['user',
            'user' => function($q)
                {
                    $q->withoutGlobalScope(CompanyScope::class);
            }
            ])
            ->take(20)
            ->get();
        }
        else{
            $recentSales = null;
        }

        $todoItemsView = $this->generateTodoView();

        return view('admin.dashboard.index', compact('recentSales', 'todoItemsView'));
    }

}
