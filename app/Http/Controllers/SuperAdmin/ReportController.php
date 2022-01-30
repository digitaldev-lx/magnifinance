<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Advertise;
use App\User;
use App\Payment;
use Carbon\Carbon;
use App\BookingItem;
use App\Helper\Reply;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SuperAdminBaseController;

class ReportController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.reports'));

    }

    public function index()
    {

        abort_403(!$this->user->is_superadmin);
        $labels = [
            'Today' => 'today',
            'Yesterday' => 'yesterday',
            'Last 7 Days' => 'lastWeek',
            'Last 30 Days' => 'lastThirtyDays',
            'This Month' => 'thisMonth',
            'Last Month' => 'lastMonth'
        ];

        return view('superadmin.report.layout', compact(['labels']));
    }

    public function earningReportChart(Request $request)
    {

        $startDate = Carbon::createFromFormat($this->settings->date_format, $request->startDate)->format('Y-m-d');
        $endDate = Carbon::createFromFormat($this->settings->date_format, $request->endDate)->format('Y-m-d');

        $payments = Payment::withoutGlobalScopes()->where('status', 'completed', 'booking')
            ->whereDate('paid_on', '>=', $startDate)
            ->whereDate('paid_on', '<=', $endDate)
            ->groupBy('year', 'month')
            ->orderBy('amount', 'ASC')
            ->get(
                [
                    DB::raw('DATE_FORMAT(paid_on,"%D-%M-%Y") as pay_date'),
                    DB::raw('DATE_FORMAT(paid_on,"%M/%y") as date'),
                    DB::raw('YEAR(paid_on) year, MONTH(paid_on) month'),
                    DB::raw('sum(amount) as total')
                ]
            );

        $graphData = [];

        foreach($payments as $key2 => $payment){
            $payments[$key2]->total = $payment->total;
            $graphData[] = $payment;
        }

        usort(
            $graphData, function ($a, $b) {
                $t1 = strtotime($a->pay_date);
                $t2 = strtotime($b->pay_date);
                return $t1 - $t2;
            }
        );

        $labels = [];

        foreach($graphData as $gData){
            $labels[] = $gData->date;
        }

        $earnings = [];

        foreach($graphData as $gData){
            $earnings[] = round($gData->total, 2);
        }

        return Reply::dataOnly(['labels' => $labels, 'data' => $earnings, 'status' => 'success']);
    }

    public function salesReportChart(Request $request)
    {

        $labels = [];
        $sales = [];

        $items = $this->salesQuery($request);

        foreach ($items as $item) {
            $labels[] = $item->item_name;
            $sales[] = $item->totalQuantity;
        }

        return Reply::dataOnly(['labels' => $labels, 'data' => $sales, 'status' => 'success']);
    }

    public function salesQuery($request)
    {

        $startDate = Carbon::createFromFormat($this->settings->date_format, $request->startDate)->format('Y-m-d');
        $endDate = Carbon::createFromFormat($this->settings->date_format, $request->endDate)->format('Y-m-d');

        $deals = BookingItem::withoutGlobalScopes()->join('bookings', 'booking_items.booking_id', 'bookings.id')
            ->join('deals', 'booking_items.deal_id', 'deals.id')
            ->join('users', 'users.id', 'bookings.user_id')
            ->join('payments', 'payments.booking_id', 'bookings.id')
            ->select(DB::raw('count(booking_items.deal_id) as totalQuantity'), DB::raw('sum(booking_items.amount)  as totalAmount'), 'users.name as customer', 'deals.title as item_name', 'payments.paid_on as payment_on')
            ->whereNotNull('booking_items.deal_id')
            ->whereDate('bookings.date_time', '>=', $startDate)
            ->whereDate('bookings.date_time', '<=', $endDate)
            ->where('bookings.payment_status', 'completed')
            ->groupBy('booking_items.deal_id');

        $services = BookingItem::withoutGlobalScopes()->join('bookings', 'booking_items.booking_id', 'bookings.id')
            ->join('business_services', 'booking_items.business_service_id', 'business_services.id')
            ->join('users', 'users.id', 'bookings.user_id')
            ->join('payments', 'payments.booking_id', 'bookings.id')
            ->select(DB::raw('count(booking_items.business_service_id) as totalQuantity'), DB::raw('sum(booking_items.amount)  as totalAmount'), 'users.name as customer', 'business_services.name as item_name', 'payments.paid_on as payment_on')
            ->whereNotNull('booking_items.business_service_id')
            ->whereDate('bookings.date_time', '>=', $startDate)
            ->whereDate('bookings.date_time', '<=', $endDate)
            ->where('bookings.payment_status', 'completed')
            ->groupBy('booking_items.business_service_id');

        $products = BookingItem::withoutGlobalScopes()->join('bookings', 'booking_items.booking_id', 'bookings.id')
            ->join('products', 'booking_items.product_id', 'products.id')
            ->join('users', 'users.id', 'bookings.user_id')
            ->join('payments', 'payments.booking_id', 'bookings.id')
            ->select(DB::raw('count(booking_items.product_id) as totalQuantity'), DB::raw('sum(booking_items.amount)  as totalAmount'), 'users.name as customer', 'products.name as item_name', 'payments.paid_on as payment_on')
            ->whereNotNull('booking_items.product_id')
            ->whereDate('bookings.date_time', '>=', $startDate)
            ->whereDate('bookings.date_time', '<=', $endDate)
            ->where('bookings.payment_status', 'completed')
            ->groupBy('booking_items.product_id')
            ->union($services)
            ->union($deals)
            ->get();

        return $products;

    }

    public function newCustomers(Request $request)
    {

        $startDate = Carbon::createFromFormat($this->settings->date_format, $request->startDate)->format('Y-m-d');
        $endDate = Carbon::createFromFormat($this->settings->date_format, $request->endDate)->format('Y-m-d');
        $dateRange = CarbonPeriod::create($startDate, $endDate);
        $data = [];
        $data ['status'] = 'success';

        foreach ($dateRange as $key => $date) {
            $data['labels'][] = date_format( $date, 'd-M');
            $data['data'][] = User::withoutGlobalScopes()->allCustomers()
                ->whereDate('created_at', '>=', $date)
                ->whereDate('created_at', '<=', $date)
                ->count();
        }

        return Reply::dataOnly($data);
    }

    public function newVendors(Request $request)
    {

        $startDate = Carbon::createFromFormat($this->settings->date_format, $request->startDate)->format('Y-m-d');
        $endDate = Carbon::createFromFormat($this->settings->date_format, $request->endDate)->format('Y-m-d');
        $dateRange = CarbonPeriod::create($startDate, $endDate);
        $data = [];
        $data ['status'] = 'success';

        foreach ($dateRange as $key => $date) {
            $data['labels'][] = date_format( $date, 'd-M');
            $data['data'][] = User::withoutGlobalScopes()->allAdministrators()
                ->whereDate('created_at', '>=', $date)
                ->whereDate('created_at', '<=', $date)
                ->count();
        }

        return Reply::dataOnly($data);
    }

    public function commissionRevenue(Request $request)
    {
        $startDate = Carbon::createFromFormat($this->settings->date_format, $request->startDate)->format('Y-m-d');
        $endDate = Carbon::createFromFormat($this->settings->date_format, $request->endDate)->format('Y-m-d');
        $dateRange = CarbonPeriod::create($startDate, $endDate);
        $data = [];
        $data ['status'] = 'success';

        foreach ($dateRange as $key => $date) {
            $data['labels'][] = date_format( $date, 'd-M');
            $data['data'][] = Payment::withoutGlobalScopes()
                ->where('status', 'completed')->whereNotNull('paid_on')
                ->whereDate('paid_on', '>=', $date)
                ->whereDate('paid_on', '<=', $date)
                ->sum('commission');
        }

        return Reply::dataOnly($data);
    }

    public function commissionRevenueTable(Request $request)
    {

        $startDate = Carbon::createFromFormat($this->settings->date_format, $request->startDate)->format('Y-m-d');
        $endDate = Carbon::createFromFormat($this->settings->date_format, $request->endDate)->format('Y-m-d');

        $payments = Payment::withoutGlobalScopes()
            ->where('status', 'completed')->whereNotNull('paid_on')
            ->whereDate('paid_on', '>=', $startDate)
            ->whereDate('paid_on', '<=', $endDate)
            ->orderByDesc('paid_on')
            ->get();

        return \datatables()->of($payments)
            ->addColumn('company_logo', function ($row) {
                return '<img src="' . $row->company->logo_url . '"  width="60em" /> ';
            })
            ->editColumn('company', function ($row) {
                return ucwords($row->company->company_name);
            })
            ->editColumn('company_owner', function ($row) {
                return ucwords($row->company->owner->name);
            })
            ->editColumn('company_income', function ($row) {
                return currencyFormatter(number_format((float)$row->company->income, 2, '.', ''));
            })
            ->editColumn('company_registered_date', function ($row) {
                return $row->company->created_at->translatedFormat($this->settings->date_format);
            })
            ->editColumn('amount', function ($row) {
                return currencyFormatter(number_format((float)$row->amount, 2, '.', ''));
            })
            ->editColumn('commission', function ($row) {
                return currencyFormatter(number_format((float)$row->commission, 2, '.', ''));
            })
            ->editColumn('paid_on', function ($row) {
                return $row->paid_on->translatedFormat($this->settings->date_format);
            })
            ->addIndexColumn()
            ->rawColumns(['company_logo'])
            ->toJson();
    }

    public function customerTable(Request $request)
    {
        $startDate = Carbon::createFromFormat($this->settings->date_format, $request->startDate)->format('Y-m-d');
        $endDate = Carbon::createFromFormat($this->settings->date_format, $request->endDate)->format('Y-m-d');
        $customer = User::withoutGlobalScopes()->with('customerBookings')->has('customerBookings')
            ->whereHas('customerBookings', function ($query) use ($startDate,$endDate) {
                $query->whereDate('date_time', '>=', Carbon::createFromFormat('Y-m-d', $startDate))
                    ->whereDate('date_time', '<=', Carbon::createFromFormat('Y-m-d', $endDate));
            })
        ->get();

        return \datatables()->of($customer)
            ->editColumn('image', function ($row) {
                return '<img src="' . $row->user_image_url. '" class="border img-bordered-sm img-circle" height="50em" width="50em" /> ';
            })
            ->editColumn('name', function ($row) {

                return ucwords($row->name);
            })
            ->editColumn('email', function ($row) {
                return $row->email ?? '--';
            })
            ->editColumn('phone', function ($row) {
                return !is_null($row->formatted_mobile) ? $row->formatted_mobile : '--';
            })
            ->editColumn('totalBookings', function ($row) {
                return $row->customerBookings->count();
            })
            ->editColumn('registeredDate', function ($row) {
                return $row->created_at->format($this->settings->date_format);
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'image', 'status'])
            ->toJson();
    }

    public function advertisesRevenue(Request $request)
    {
        $startDate = Carbon::createFromFormat($this->settings->date_format, $request->startDate)->format('Y-m-d');
        $endDate = Carbon::createFromFormat($this->settings->date_format, $request->endDate)->format('Y-m-d');
        $dateRange = CarbonPeriod::create($startDate, $endDate);
        $data = [];
        $data ['status'] = 'success';

        foreach ($dateRange as $key => $date) {
            $data['labels'][] = date_format( $date, 'd-M');
            $data['data'][] = Advertise::withoutGlobalScopes()
                ->where('status', 'completed')->whereNotNull('paid_on')
                ->whereDate('paid_on', '>=', $date)
                ->whereDate('paid_on', '<=', $date)
                ->sum('amount');
        }

        return Reply::dataOnly($data);
    }

    public function advertisesRevenueTable(Request $request)
    {

        $startDate = Carbon::createFromFormat($this->settings->date_format, $request->startDate)->format('Y-m-d');
        $endDate = Carbon::createFromFormat($this->settings->date_format, $request->endDate)->format('Y-m-d');

        $payments = Advertise::withoutGlobalScopes()
            ->where('status', 'completed')->whereNotNull('paid_on')
            ->whereDate('paid_on', '>=', $startDate)
            ->whereDate('paid_on', '<=', $endDate)
            ->orderByDesc('paid_on')
            ->get();

        return \datatables()->of($payments)
            ->addColumn('company_logo', function ($row) {
                return '<img src="' . $row->company->logo_url . '"  width="60em" /> ';
            })
            ->editColumn('company', function ($row) {
                return ucwords($row->company->company_name);
            })
            ->editColumn('company_owner', function ($row) {
                return ucwords($row->company->owner->name);
            })
            ->editColumn('avertising_expenses', function ($row) {
                return currencyFormatter(number_format((float)$row->company->expenses, 2, '.', ''));
            })

            ->editColumn('amount', function ($row) {
                return currencyFormatter(number_format((float)$row->amount, 2, '.', ''));
            })

            ->editColumn('paid_on', function ($row) {
                return $row->paid_on->translatedFormat($this->settings->date_format);
            })
            ->addIndexColumn()
            ->rawColumns(['company_logo'])
            ->toJson();
    }

} /* end of class */
