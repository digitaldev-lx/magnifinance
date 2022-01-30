<?php

namespace App\Http\Controllers\Admin;

use App\Tax;
use App\Role;
use App\User;
use DateTime;
use App\Booking;
use App\Payment;
use App\Product;
use App\Category;
use App\Location;
use Carbon\Carbon;
use App\BookingItem;
use App\Helper\Reply;
use App\BusinessService;
use App\Scopes\CompanyScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminBaseController;

class ReportController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.reports'));

        $this->middleware(function ($request, $next) {

            if(!in_array('Reports', $this->user->modules)){
                abort(403);
            }

            return $next($request);
        });
    }

    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $labels = [
            'Today' => 'today',
            'Yesterday' => 'yesterday',
            'Last 7 Days' => 'lastWeek',
            'Last 30 Days' => 'lastThirtyDays',
            'This Month' => 'thisMonth',
            'Last Month' => 'lastMonth'
        ];

        $users_ids = Booking::with('users')->pluck('user_id')->unique();
        $customers = User::withoutGlobalScopes()->whereIn('id', $users_ids)->pluck('name')->unique()->sort();
        $locations = Location::all();
        $staffs = User::select('id', 'name')->with('roles')->whereHas('roles', function($q){
            $q->where('name', '<>', 'customer');
        })->get();
        $status = \request('status');
        $services = BusinessService::select('name')->groupBy('name')->get();
        $products = Product::select('name')->groupBy('name')->get();

        return view('admin.report.layout', compact(['labels', 'customers', 'staffs', 'services', 'products', 'status','locations']));
    }

    public function earningReportChart(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $payments = Payment::where('status', 'completed', 'booking')
            ->whereDate('paid_on', '>=', Carbon::createFromFormat('Y-m-d', $request->startDate))
            ->whereDate('paid_on', '<=', Carbon::createFromFormat('Y-m-d', $request->endDate))
            ->whereHas('booking.items.businessService.location', function($query) use($request){
                $query->where('id', $request->location);
            })
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

    public function earningTable(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $bookings = Booking::where('payment_status', 'completed')
            ->whereHas('completedPayment', function ($query) use ($request) {
                $query->whereDate('paid_on', '>=', Carbon::createFromFormat('Y-m-d', $request->startDate))
                    ->whereDate('paid_on', '<=', Carbon::createFromFormat('Y-m-d', $request->endDate));
            })
            ->whereHas('items.businessService.location', function($query) use($request){
                $query->where('id', $request->location);
            })
            ->with([
                'completedPayment' => function($q) { $q->withoutGlobalScope(CompanyScope::class);
                } ,
                'items' => function($q) { $q->withoutGlobalScope(CompanyScope::class);
                },
                'user' => function($q) { $q->withoutGlobalScope(CompanyScope::class);
                },
            ])
            ->get();

        return \datatables()->of($bookings)
            ->editColumn('user_id', function ($row) {

                return ucwords($row->user->name);
            })
            ->editColumn('amount_to_pay', function ($row) {
                return currencyFormatter(number_format((float)$row->amount_to_pay, 2, '.', ''), myCurrencySymbol());
            })
            ->editColumn('date_time', function ($row) {
                return $row->completedPayment->paid_on->translatedFormat($this->settings->date_format);
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'image', 'status'])
            ->toJson();
    }

    public function salesReportChart(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $labels = [];
        $sales = [];

        $items = $this->salesQuery($request);

        foreach ($items as $item) {
            $labels[] = $item->item_name;
            $sales[] = $item->totalQuantity;
        }

        return Reply::dataOnly(['labels' => $labels, 'data' => $sales, 'status' => 'success']);
    }

    public function salesTable(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);
        $taxes = Tax::all();

        $items = $this->salesQuery($request);

        $this->taxAmount = 0;
        return \datatables()->of(collect($items))
            ->editColumn('service_name', function ($row) {
                return $row->item_name;
            })
            ->editColumn('customer_name', function ($row) {
                return $row->customer;
            })
            ->editColumn('sales', function ($row) {
                return $row->totalQuantity;
            })
            ->editColumn('tax', function ($row) use ($taxes){
                $rec = '<ol>';

                if ($row->businessService)
                {
                    foreach($row->businessService->taxServices as $tax)
                    {
                        $taxRecord = $taxes->filter(function ($value, $key) use ($tax) {
                            return $value->id == $tax->tax_id;
                        })->first();

                        $this->taxAmount += ($taxRecord->percent * $row->totalAmount) / 100;

                        $rec .= '<li>'.$taxRecord->name . ' - ' . $taxRecord->percent.'%</li>';
                    }
                }

                if ($row->deal)
                {
                    foreach($row->deal->dealTaxes as $tax)
                    {
                        $taxRecord = $taxes->filter(function ($value, $key) use ($tax) {
                            return $value->id == $tax->tax_id;
                        })->first();

                        $this->taxAmount += ($taxRecord->percent * $row->totalAmount) / 100;

                        $rec .= '<li>'.$taxRecord->name . ' - ' . $taxRecord->percent.'%</li>';
                    }
                }

                if ($row->product)
                {
                    foreach($row->product->productTaxes as $tax)
                    {
                        $taxRecord = $taxes->filter(function ($value, $key) use ($tax) {
                            return $value->id == $tax->tax_id;
                        })->first();

                        $this->taxAmount += ($taxRecord->percent * $row->totalAmount) / 100;

                        $rec .= '<li>'.$taxRecord->name . ' - ' . $taxRecord->percent.'%</li>';
                    }
                }

                $rec .= '</ol>';
                return $rec;
            })
            ->editColumn('amount', function ($row) {
                $amount = currencyFormatter(($row->totalAmount + $this->taxAmount), myCurrencySymbol());
                $this->taxAmount = 0;
                return $amount;
            })
            ->editColumn('paid_on', function ($row) {
                return isset($row->payment_on) ? Carbon::parse($row->payment_on)->translatedFormat($this->settings->date_format) : '--';
            })
            ->rawColumns(['tax'])
            ->addIndexColumn()
            ->toJson();
    }

    public function tabularTable(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $items = [];
        $bookings = Booking::with([
            'users' => function($q) { $q->withoutGlobalScope(CompanyScope::class);
            } ,
            'items' => function($q) { $q->withoutGlobalScope(CompanyScope::class);
            } ,
            'user' => function($q) { $q->withoutGlobalScope(CompanyScope::class);
            },
            'payment' => function($q) { $q->withoutGlobalScope(CompanyScope::class);
            }
        ]);

        if($request->from_date && $request->to_date){
            $bookings->whereDate('date_time', '>=', $request->from_date)->whereDate('date_time', '<=', $request->to_date);
        }

        if ($request->customer_name) {
            $customer = $request->customer_name;
            $bookings->whereHas('user', function ($query) use ($customer) {
                $query->where('name', $customer);
            });
        }

        if($request->service_name){
            $bookings->whereHas('items.businessService', function ($q) use ($request) {
                $q->where('name', $request->service_name);
            });
        }

        if($request->product_name){
            $bookings->whereHas('items.product', function ($q) use ($request) {
                $q->where('name', $request->product_name);
            });
        }

        if($request->employee_id){
            $bookings->whereHas('users', function ($q) use ($request) {
                $q->where('id', $request->employee_id);
            });
        }

        if($request->booking_status){
            $bookings->where('status', $request->booking_status);
        }

        if($request->booking_type){
            if($request->booking_type == 'deal'){
                $bookings->where('deal_id', '<>', '');
            }
            else{
                $bookings->where('deal_id', null);
            }
        }

        if($request->location){
            $bookings->whereHas('items.businessService.location', function($query) use($request){
                $query->where('id', $request->location);
            });
        }

        if ($request->payment){
            $bookings->where('payment_status', $request->payment);
        }

        $bookings = $bookings->orderBy('id', 'desc')->get();
        $total = 0;

        foreach ($bookings as $booking){
            $total += $booking->amount_to_pay;
            $items[] = $booking;
        }

        return \datatables()->of(collect($items))
            ->editColumn('service_name', function ($row) {
                $booking_items = '<ol>';

                foreach($row->items as $item)
                {
                    $item_name = '';

                    if(!is_null($item->deal_id) && is_null($item->business_service_id) && is_null($item->product_id)) {
                        $item_name = ucwords($item->deal->title);
                    }
                    else if(is_null($item->deal_id) && !is_null($item->business_service_id) && is_null($item->product_id)) {
                        $item_name = ucwords($item->businessService->name);
                    }
                    else if(is_null($item->deal_id) && is_null($item->business_service_id) && !is_null($item->product_id)) {
                        $item_name = $item->product->name;
                    }

                    $booking_items .= '<li>'.$item_name.' <b> X'.$item->quantity.'</b></li>';
                }

                $booking_items .= '</ol>';
                return $booking_items;
            })
            ->editColumn('customer_name', function ($row) {
                return '<i class="icon-user"></i> '.$row->user->name;
            })
            ->editColumn('employee_name', function ($row) {
                $booking_users = '';

                foreach($row->users as $user){
                    $booking_users .= '<i class="icon-user"></i> '. ucfirst($user->name).' &nbsp;&nbsp;';
                }

                if($booking_users == ''){ return __('app.notAvailable');
                }

                return $booking_users;
            })
            ->editColumn('tax', function ($row) {
                    return currencyFormatter($row->tax_amount, myCurrencySymbol());
            })
            ->editColumn('amount', function ($row) {
                $status = $row->payment != null ? $row->payment->status : '';

                if($status == 'completed'){
                    return '<i data-toggle="tooltip" data-original-title="'.__('app.payment').' '.__('app.done').'" class="fa fa-check-square amount-complete"></i> '.currencyFormatter($row->amount_to_pay, myCurrencySymbol());
                }
                else {
                    return ' <i data-toggle="tooltip" data-original-title="'.__('app.payment').' '.__('app.pending').'" class="fa fa-times-circle amount-due"></i> '.currencyFormatter($row->amount_to_pay, myCurrencySymbol());
                }
            })
            ->editColumn('booking_time', function ($row) {

                if($this->validateDate($row->date_time)){
                    return $row->date_time->translatedFormat($this->settings->time_format);
                }

                return __('app.notAvailable');
            })
            ->editColumn('booking_source', function ($row) {
                return $row->source;
            })
            ->editColumn('booking_status', function ($row) {

                $style = '';

                if($row->status == 'approved'){
                    $style = 'border-info text-info';
                }

                if($row->status == 'approved'){
                    $style = 'border-info text-info';
                }
                elseif($row->status == 'completed'){
                    $style = 'border-success text-success';
                }
                elseif($row->status == 'pending'){
                    $style = 'border-warning text-warning';
                }
                elseif($row->status == 'in progress'){
                    $style = 'border-primary text-primary';
                }
                else{
                    $style = 'border-danger text-danger';
                }

                return '<span class="text-uppercase small border badge-pill '.$style.'">'.$row->status.'</span>';
            })
            ->editColumn('booking_date', function ($row) {

                if($this->validateDate($row->date_time)){
                    return $row->date_time->translatedFormat($this->settings->date_format);
                }

                return __('app.notAvailable');
            })
            ->addIndexColumn()
            ->rawColumns(['employee_name', 'customer_name', 'booking_status', 'service_name', 'amount', 'tax'])
            ->with('sums', currencyFormatter($total, myCurrencySymbol()))
            ->make(true);
    }

    public function userTypeChart()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];

        $record = Role::withoutGlobalScopes()
            ->withCount('users')
            ->groupBy('display_name')
            ->get();

        foreach($record as $row)
        {
            $data['label'][] = $row->display_name;
            $data['data'][] = $row->users_count;
        }

        return Reply::dataOnly(['data' => $data]);

    }

    public function serviceTypeChart()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];

        $record = Category::withCount('services')->get();

        foreach($record as $row)
        {
            $data['label'][] = $row->name;
            $data['data'][] = (int)$row->services_count;
        }

        return Reply::dataOnly(['data' => $data]);

    }

    public function bookingSourceChart()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];

        $record = Booking::groupBy('source')
                ->get([
                    'source as booking_source',
                    DB::raw('(select count(bookings.source) from `bookings` where source=booking_source) as countSource')
                ]);

        foreach($record as $row)
        {
            $data['label'][] = $row->booking_source;
            $data['data'][] = $row->countSource;
        }

        return Reply::dataOnly(['data' => $data]);

    }

    public function bookingPerDayChart(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];

        $record = Booking::select('id', 'date_time', 'source', DB::raw('count(*) as total'))->whereDate('date_time', Carbon::createFromFormat('Y-m-d', $request->booking_date))
            ->groupBy('source')
            ->get();

        foreach($record as $row)
        {
            $data['label'][] = $row->source;
            $data['data'][] = (int)$row->total;
        }

        return Reply::dataOnly(['data' => $data]);

    }

    public function bookingPerMonthChart(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];
        $check_month = [];
        $day_array = [];
        $no_of_days = Carbon::createFromFormat('Y-m', $request->booking_month)->daysInMonth;

        for ($i = 1; $i <= $no_of_days; $i++)
        {
            array_push($day_array, $i);
        }

        $record = Booking::select('id', 'date_time', DB::raw('DATE(date_time) as date'), DB::raw('count(*) as total'))
            ->whereMonth('date_time', Carbon::createFromFormat('Y-m', $request->booking_month))
            ->whereYear('date_time', Carbon::createFromFormat('Y-m', $request->booking_month))
            ->groupBy('date')
            ->get();

        foreach ($day_array as $key1 => $value)
        {
            /* if month is availble in table */
            foreach($record as $key2 => $row)
            {
                if(in_array(Carbon::parse($row->date)->format('d'), $day_array) && $day_array[$key1] == Carbon::parse($row->date)->format('d'))
                {
                    array_push($check_month, Carbon::parse($row->date)->format('d'));
                    $data['label'][] = $day_array[$key1];
                    $data['data'][] = (int)$row->total;
                }
            }

            /* if month is not availble in table */
            if(in_array($day_array[$key1], $day_array) && !in_array($day_array[$key1], $check_month))
            {
                $data['label'][] = $day_array[$key1];
                $data['data'][] = 0;

            }
        }

        return Reply::dataOnly(['data' => $data]);
    }

    public function bookingPerYearChart(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];
        $check_month = [];
        $months_array = [1,2,3,4,5,6,7,8,9,10,11,12];

        $record = Booking::whereYear('date_time', $request->booking_year)
        ->groupBy('year', 'month')
        ->get(
            [
                DB::raw('COUNT(id) as `total_bookings`'),
                DB::raw('YEAR(date_time) year, MONTH(date_time) month')
            ]
        );

        foreach ($months_array as $key1 => $value)
        {
            /* if month is available on table */
            foreach($record as $key2 => $row)
            {
                if(in_array($row->month, $months_array) && $row->month == $months_array[$key1])
                {
                    array_push($check_month, $row->month);
                    $data['label'][] = DateTime::createFromFormat('m', $row->month)->format('M');
                    $data['data'][] = (int)$row->total_bookings;
                }
            }

            /* if month is not available on table */
            if(in_array($months_array[$key1], $months_array) && !in_array($months_array[$key1], $check_month))
            {
                $data['label'][] = DateTime::createFromFormat('m', $months_array[$key1])->format('M');
                $data['data'][] = 0;
            }
        }

        return Reply::dataOnly(['data' => $data]);
    }

    public function paymentPerDayChart(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];

        $record = Payment::select('id', 'paid_on', 'gateway', DB::raw('sum(amount) as total'))
            ->where('status', 'completed')
            ->whereDate('paid_on', Carbon::createFromFormat('Y-m-d', $request->payment_date))
            ->groupBy('gateway')->get();

        foreach($record as $row)
        {
            $data['label'][] = $row->gateway;
            $data['data'][] = (int)$row->total;
        }

        return Reply::dataOnly(['data' => $data]);
    }

    public function paymentPerMonthChart(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];
        $check_month = [];
        $no_of_days = Carbon::createFromFormat('Y-m', $request->payment_month)->daysInMonth;
        $day_array = [];

        for ($i = 1; $i <= $no_of_days; $i++)
        {
            array_push($day_array, $i);
        }

        $record = Payment::select('paid_on', DB::raw('DATE(paid_on) as date'), DB::raw('sum(amount) as total'))
            ->where('status', 'completed')
            ->whereMonth('paid_on', Carbon::createFromFormat('Y-m', $request->payment_month))
            ->whereYear('paid_on', Carbon::createFromFormat('Y-m', $request->payment_month))
            ->groupBy('date')
            ->get();

        foreach ($day_array as $key1 => $value)
        {
            /* if day is available in table */
            foreach($record as $key2 => $row)
            {

                if(in_array(Carbon::parse($row->paid_on)->format('d'), $day_array) && $day_array[$key1] == Carbon::parse($row->paid_on)->format('d'))
                {
                    array_push($check_month, Carbon::parse($row->paid_on)->format('d'));
                    $data['label'][] = $day_array[$key1];
                    $data['data'][] = (int)$row->total;
                }
            }

            /* if day is not available in table */
            if(in_array($day_array[$key1], $day_array) && !in_array($day_array[$key1], $check_month))
            {
                $data['label'][] = $day_array[$key1];
                $data['data'][] = 0;

            }
        }

        return Reply::dataOnly(['data' => $data]);

    }

    public function paymentPerYearChart(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);

        $data = [];
        $check_month = [];
        $months_array = [1,2,3,4,5,6,7,8,9,10,11,12];

        $record = Payment::whereYear('paid_on', $request->payment_year)
        ->where('status', 'completed')
        ->groupBy('year', 'month')
        ->get(
            [
                DB::raw('SUM(amount) as `total_amount`'),
                DB::raw('YEAR(paid_on) year, MONTH(paid_on) month')
            ]
        );

        foreach ($months_array as $key1 => $value)
        {
            /* if month is availble on table */
            foreach($record as $key2 => $row)
            {
                if(in_array($row->month, $months_array) && $row->month == $months_array[$key1])
                {
                    array_push($check_month, $row->month);
                    $data['label'][] = DateTime::createFromFormat('m', $row->month)->format('M');
                    $data['data'][] = (int)$row->total_amount;
                }
            }

            /* if month is not available in table */
            if(in_array($months_array[$key1], $months_array) && !in_array($months_array[$key1], $check_month))
            {
                $data['label'][] = DateTime::createFromFormat('m', $months_array[$key1])->format('M');
                $data['data'][] = 0;
            }
        }

        return Reply::dataOnly(['data' => $data]);
    }

    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function salesQuery($request)
    {

        $deals = BookingItem::with(['deal','deal.dealTaxes'])->withoutGlobalScopes()->join('bookings', 'booking_items.booking_id', 'bookings.id')
            ->join('deals', 'booking_items.deal_id', 'deals.id')
            ->join('users', 'users.id', 'bookings.user_id')
            ->join('payments', 'payments.booking_id', 'bookings.id')
            ->select(DB::raw('count(booking_items.deal_id) as totalQuantity'), DB::raw('sum(booking_items.amount)  as totalAmount'), DB::raw('count(users.id) as customer'), 'deals.title as item_name', 'payments.paid_on as payment_on', 'booking_items.deal_id')
            ->whereNotNull('booking_items.deal_id')
            ->where('bookings.location_id', $request->location)
            ->whereMonth('bookings.date_time', $request->month)
            ->whereYear('bookings.date_time', $request->year)
            ->where('bookings.payment_status', 'completed')
            ->groupBy('booking_items.deal_id');

        $services = BookingItem::with(['businessService','businessService.taxServices'])->withoutGlobalScopes()->join('bookings', 'booking_items.booking_id', 'bookings.id')
            ->join('business_services', 'booking_items.business_service_id', 'business_services.id')
            ->join('users', 'users.id', 'bookings.user_id')
            ->join('payments', 'payments.booking_id', 'bookings.id')
            ->select(DB::raw('count(booking_items.business_service_id) as totalQuantity'), DB::raw('sum(booking_items.amount)  as totalAmount'), DB::raw('count(users.id) as customer'), 'business_services.name as item_name', 'payments.paid_on as payment_on', 'booking_items.business_service_id')
            ->whereNotNull('booking_items.business_service_id')
            ->where('bookings.location_id', $request->location)
            ->whereMonth('bookings.date_time', $request->month)
            ->whereYear('bookings.date_time', $request->year)
            ->where('bookings.payment_status', 'completed')
            ->groupBy('booking_items.business_service_id');

        $products = BookingItem::with(['product','product.productTaxes'])->withoutGlobalScopes()->join('bookings', 'booking_items.booking_id', 'bookings.id')
            ->join('products', 'booking_items.product_id', 'products.id')
            ->join('users', 'users.id', 'bookings.user_id')
            ->join('payments', 'payments.booking_id', 'bookings.id')
            ->select(DB::raw('count(booking_items.product_id) as totalQuantity'), DB::raw('sum(booking_items.amount)  as totalAmount'), DB::raw('count(users.id) as customer'), 'products.name as item_name', 'payments.paid_on as payment_on', 'booking_items.product_id')
            ->whereNotNull('booking_items.product_id')
            ->where('bookings.location_id', $request->location)
            ->whereMonth('bookings.date_time', $request->month)
            ->whereYear('bookings.date_time', $request->year)
            ->where('bookings.payment_status', 'completed')
            ->groupBy('booking_items.product_id')
            ->union($services)
            ->union($deals)
            ->get();

            return $products;

    }

    public function customerTable(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'), 403);
        $customer = User::withoutGlobalScopes()->with('customerBookings')->has('customerBookings')
            ->whereHas('customerBookings', function ($query) use ($request) {
                $query->whereDate('date_time', '>=', Carbon::createFromFormat('Y-m-d', $request->startDate))
                    ->whereDate('date_time', '<=', Carbon::createFromFormat('Y-m-d', $request->endDate));
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

} /* end of class */
