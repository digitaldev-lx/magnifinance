<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Booking\UpdateBooking;
use App\Http\Requests\BookingStatusMultiUpdate;
use App\Http\Requests\StoreFrontBooking;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\BookingTime;
use App\Models\BusinessService;
use App\Models\Category;
use App\Models\Company;
use App\Models\Coupon;
use App\Models\Deal;
use App\Models\EmployeeSchedule;
use App\Models\GatewayAccountDetail;
use App\Models\ItemTax;
use App\Models\Leave;
use App\Models\Location;
use App\Models\OfficeLeave;
use App\Models\Payment;
use App\Models\PaymentGatewayCredentials;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Role;
use App\Models\Tax;
use App\Notifications\BookingCancel;
use App\Notifications\BookingReminder;
use App\Notifications\SendPaymentLinkNotification;
use App\Scopes\CompanyScope;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Stripe\Stripe;

class BookingController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->credentials = PaymentGatewayCredentials::first();
        $setting = Company::with('currency')->first();

        view()->share('pageTitle', __('menu.bookings'));
        view()->share('credentials', $this->credentials);
        view()->share('setting', $setting);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_booking') && !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        if (\request()->ajax()) {
            $bookings = Booking::orderBy('date_time', 'desc')
                ->with([
                    'user' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    }
                ]);

            if (\request('filter_sort') != '') {
                $bookings->orderBy('id', \request('filter_sort'));
            }

            if (\request('filter_status') != '') {
                $bookings->where('bookings.status', \request('filter_status'));
            }

            if (\request('filter_customer') != '') {
                $customer = request()->filter_customer;
                $bookings->where('user_id', $customer);
            }

            if (\request('filter_location') != '') {
                $bookings->leftJoin('booking_items', 'bookings.id', 'booking_items.booking_id')
                    ->leftJoin('business_services', 'booking_items.business_service_id', 'business_services.id')
                    ->leftJoin('locations', 'business_services.location_id', 'locations.id')
                    ->select('bookings.*')
                    ->where('locations.id', request('filter_location'))
                    ->groupBy('bookings.id');
            }

            if (\request('filter_date') != '') {
                $startTime = Carbon::createFromFormat('Y-m-d', request('filter_date'), $this->settings->timezone)->setTimezone('UTC')->startOfDay();
                $endTime = $startTime->copy()->addDay()->subSecond();

                $bookings->whereBetween('bookings.date_time', [$startTime, $endTime]);
            }

            if (!$this->user->is_admin && !$this->user->can('create_booking')) {
                ($this->user->is_employee) ? $bookings->whereHas('users', function ($q) {
                    $q->where('user_id', $this->user->id);
                })->orWhere('user_id', $this->user->id) : $bookings->where('bookings.user_id', $this->user->id);
            }

            $bookings = $bookings->get();

            return \datatables()->of($bookings)
                ->editColumn('id', function ($row) {
                    $view = view('admin.booking.list_view', compact('row'))->render();
                    return $view;
                })
                ->rawColumns(['id'])
                ->toJson();
        }

        $customers = User::withoutGlobalScopes()->has('customerBookings')->get();

        $locations = Location::all();
        $status = \request('status');

        return view('admin.booking.index', compact('customers', 'status', 'locations'));
    }

    public function calendar()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_booking') && !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);
        $bookings = [];

        if ($this->user->hasRole('customer')) {
            $bookings = Booking::with([
                'user' => function ($q) {
                    $q->withoutGlobalScope(CompanyScope::class)->where('id', $this->user->id);
                }
            ])->where('status', '!=', 'canceled')->where('user_id', $this->user->id)->get();
        }
        elseif ($this->user->hasRole('employee')) {
            $bookings = Booking::with([
                'user' => function ($q) {
                    $q->withoutGlobalScope(CompanyScope::class);
                }
            ])->where(function ($q) {
                $q->where('status', '!=', 'canceled');
                $q->where(function ($q) {
                    $q->where('user_id', $this->user->id);
                    $q->orWhere(function ($q) {
                        $q->whereHas('users', function ($q) {
                            $q->where('id', $this->user->id);
                        });
                    });
                });
            })->get();
        }
        elseif ($this->user->is_admin) {
            $bookings = Booking::with([
                'user' => function ($q) {
                    $q->withoutGlobalScope(CompanyScope::class);
                }
            ])->where(function ($q) {
                $q->where('status', '!=', 'canceled');
            })->get();
        }

        return view('admin.booking.calendar_index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        $services = BusinessService::active()->get();
        $categories = Category::active()
            ->with(['services' => function ($query) {
                $query->active();
            }])->withoutGlobalScope(CompanyScope::class)->has('services', '>', 0)->get();
        $locations = Location::withoutGlobalScope(CompanyScope::class)->get();
        $tax = Tax::active()->first();
        $taxes = Tax::active()->get();
        $employees = User::OtherThanCustomers()->get();
        $stripeAccountDetails = GatewayAccountDetail::activeConnectedOfGateway('stripe')->first();

        $bookingDetails = [];

        if (request()->hasCookie('bookingDetails')) {
            $bookingDetails = json_decode(request()->cookie('bookingDetails'), true);
        }

        if (request()->ajax()) {
            return Reply::dataOnly(['status' => 'success', 'productsCount' => $this->productsCount]);
        }

        $locale = App::getLocale();

        return view('admin.booking.create', compact('services', 'categories', 'locations', 'taxes', 'tax', 'employees', 'bookingDetails', 'locale', 'stripeAccountDetails'));
    }

    public function posPrePayment(StoreFrontBooking $request)
    {
        $user = User::withoutGlobalScopes()->whereId($request->user_id)->firstOrFail();
        $originalAmount = $taxAmount = $amountToPay = $discountAmount = $couponDiscountAmount = 0;
        $source = "pos";
        $bookingItems = array();

        $companyId = 0;

        $tax = 0;
        $taxName = [];
        $taxPercent = 0;
        $Amt = 0;
        for ($i = 0; $i < count($request->cart_prices); $i++) {
            $service = BusinessService::findOrFail($request->cart_services[$i]);
            $location = $service->location->id;
            $taxes = ItemTax::with('tax')->where('service_id', $service->id)->get();

            $tax = 0;

            foreach ($taxes as $key => $value) {
                $tax += $value->tax->percent;
                $taxName[] = $value->tax->name;
                $taxPercent += $value->tax->percent;
            }

            $companyId = auth()->user()->company_id;

            $amount = convertedOriginalPrice($companyId, ($request->cart_quantity[$i] * $service->net_price));

            $x = $amount * ($taxPercent / 100);
            $x = $x * $request->cart_quantity[$i];

            $taxAmount += $x;

            $originalAmount += $amount;

            $deal_id = null;
            $business_service_id = $service->id;

            $bookingItems[] = [
                'business_service_id' => $business_service_id,
                'quantity' => $request->cart_quantity[$i],
                'unit_price' => convertedOriginalPrice($companyId, $service->net_price),
                'amount' => $amount,
                'deal_id' => $deal_id,
            ];

        }

        $amountToPay = ($originalAmount);

        if($request->prepayment_discount_percent > 0){
            $amountToPay = $amountToPay - $amountToPay * ($request->prepayment_discount_percent / 100);
        }

        $amountToPay = round($amountToPay, 2);
        $dateTime = Carbon::createFromFormat('Y-m-d', $request->date)->format('Y-m-d') . ' ' . Carbon::createFromFormat('H:i:s', $request->booking_time)->format('H:i:s');

        $currencyId = Company::withoutGlobalScope(CompanyScope::class)->find($companyId)->currency_id;


        try {
            DB::beginTransaction();

            $booking = new Booking();
            $booking->company_id = $companyId;
            $booking->user_id = $user->id;
            $booking->currency_id = $currencyId;
            $booking->date_time = $dateTime;
            $booking->status = 'pending';
            $booking->payment_gateway = 'card';
            $booking->original_amount = $originalAmount;
            $booking->discount = $discountAmount;
            $booking->prepayment_discount_percent = $request->prepayment_discount_percent ?? 0;
            $booking->discount_percent = '0';
            $booking->payment_status = 'pending';
            $booking->additional_notes = $request->additional_notes;
            $booking->location_id = $location;
            $booking->source = $source;

            if (!is_null($tax)) {
                $booking->tax_name = json_encode($taxName);
                $booking->tax_percent = $taxPercent;
                $booking->tax_amount = $taxAmount;
            }

            if(isset($couponData)){
                if (count($couponData) > 0 && !is_null($couponData)) {
                    $booking->coupon_id = $couponData[0]['id'];
                    $booking->coupon_discount = $couponDiscountAmount;
                    $coupon = Coupon::findOrFail($couponData[0]['id']);
                    $coupon->used_time = ($coupon->used_time + 1);
                    $coupon->save();
                }
            }


            foreach ($bookingItems as $key => $bookingItem) {
                if ($bookingItem['deal_id']) {
                    $deal = Deal::findOrFail($bookingItem['deal_id']);
                    $deal->used_time = ((int)$deal->used_time + 1);
                    $deal->update();
                }
            }

            $booking->amount_to_pay = $amountToPay;
            $booking->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort_and_log(403, $e->getMessage());
        }

        if (isset($request->employee) && count($request->employee) > 0) {
            $booking->users()->attach($request->employee);
        } else {
            if ($this->suggestEmployee($booking->date_time, $request->cart_services)) {
                $booking->users()->attach($this->suggestEmployee($booking->date_time, $request->cart_services));
            }
        }


        foreach ($bookingItems as $key => $bookingItem) {
            $bookingItems[$key]['booking_id'] = $booking->id;
            $bookingItems[$key]['company_id'] = $companyId;
        }

        try {
            DB::beginTransaction();
            DB::table('booking_items')->insert($bookingItems);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort_and_log(403, $e->getMessage());
        }

        $booking = $booking->load("items");
        $stripeAccountDetails = GatewayAccountDetail::activeConnectedOfGateway('stripe')->first();
        $paymentCredentials = PaymentGatewayCredentials::withoutGlobalScopes()->first();

        /** setup Stripe credentials **/
        Stripe::setApiKey($paymentCredentials->stripe_secret);

        $line_items = [];
        foreach ($booking->items as $key => $value) {

            /*if ($value->businessService->tax_on_price_status == 'inactive') {
                $price = ($value->business_service_id == null) ?
                    $value->unit_price * 100 :
                    ($value->unit_price * $value->businessService->taxServices[0]->tax->percent) + $value->unit_price * 100;
            } else {
                $price = $value->unit_price * 100;
            }*/
//            $price = $value->net_price * 100;

            $price = $value->unit_price * 100;


            $name = ($value->business_service_id == null) ? $value->product->name ?? 'deal' : $value->businessService->name;

            if($request->prepayment_discount_percent !== 0 || !is_null($request->prepayment_discount_percent)){
                $price = $price - $price * ($request->prepayment_discount_percent / 100);
            }

            $line_items[] = [
                'name' => $name,
                'amount' => round(currencyConvertedPrice($value->company_id, $price)),
                'currency' => $this->settings->currency->currency_code,
                'quantity' => $value->quantity,
            ];
        }

        $amount = $booking->converted_amount_to_pay * 100;
        $destination = $stripeAccountDetails ? $stripeAccountDetails->account_id : '';

        $applicationFee = round((($amount / 100) * $paymentCredentials->stripe_commission_percentage), 0);

        $data = [
            'metadata' => ["total_amount" => $request->totalAmount],
            'payment_method_types' => ['card'],
            'line_items' => [$line_items],
            'payment_intent_data' => [
                'application_fee_amount' => $applicationFee,
                'transfer_data' => [
                    'destination' => $destination,
                ],
            ],
            'success_url' => route('front.afterStripePayment', ['return_url' => 'POSPayment', 'booking_id' => $booking->id]),
            'cancel_url' => route('admin.dashboard')
        ];


        $session = \Stripe\Checkout\Session::create($data);
        $booking->stripe_session_id = $session->id;
        $booking->amount_to_pay = $request->totalAmount;
        $booking->save();
        $user->notify(new SendPaymentLinkNotification($session));

        return Reply::success(__('app.paymentRequestSent'));
        /*$superadmins = User::notCustomer()->withoutGlobalScopes()->whereNull('company_id')->get();
        Notification::send($superadmins, new ToutPurchased($tout));*/

    }

    public function askPaymentModal($amount)
    {
        $this->amount = $amount;
        return view('admin.booking.checkout_modal', $this->data);
    }

    public function bookingSlots(Request $request)
    {
        $user = User::withoutGlobalScopes()->where('id',$request->user_id)->firstOrFail();
        $company = company();

        if (!is_null($this->user) && $company->booking_per_day != (0 || '') && $company->booking_per_day <= $user->userBookingCount(Carbon::createFromFormat('Y-m-d', $request->bookingDate))) {
            $msg = __('messages.reachMaxBooking') . Carbon::createFromFormat('Y-m-d', $request->bookingDate)->format('Y-m-d');
            return Reply::dataOnly(['status' => 'fail', 'msg' => $msg]);
        }

        $bookingDate = Carbon::createFromFormat('Y-m-d', $request->bookingDate);
        $day = $bookingDate->format('l');
        $bookingTime = BookingTime::where('day', strtolower($day))->first();
        // Check if multiple booking allowed

        $bookings = Booking::whereDate('date_time', '=', $bookingDate->format('Y-m-d'));

        $officeLeaves = OfficeLeave::where('start_date', '<=', $bookingDate)
            ->where('end_date', '>=', $bookingDate)
            ->get();

        if ($officeLeaves->count() > 0) {
            $msg = __('messages.ShopClosed');
            return Reply::dataOnly(['status' => 'shopclosed', 'msg' => $msg]);

        }

        if ($bookingTime->per_day_max_booking != (0 || '') && $bookingTime->per_day_max_booking <= $bookings->count()) {
            $msg = __('messages.reachMaxBookingPerDay') . Carbon::createFromFormat('Y-m-d', $request->bookingDate)->format('Y-m-d');
            return Reply::dataOnly(['status' => 'fail', 'msg' => $msg]);
        }

        if ($bookingTime->multiple_booking == 'no') {
            $bookings = $bookings->get();
        } else {
            $bookings = $bookings->whereRaw('DAYOFWEEK(date_time) = ' . ($bookingDate->dayOfWeek + 1))->get();
        }

        $variables = compact('bookingTime', 'bookings', 'company');
        if ($bookingTime->status == 'enabled') {
            $date = Carbon::createFromDate($bookingDate)->format($company->date_format);
            if ($bookingDate->format("Y-m-d") === Carbon::today()->format("Y-m-d")) {
                $startTime = Carbon::createFromFormat($this->settings->time_format, $bookingTime->utc_start_time);

                while ($startTime->lessThan(Carbon::now())) {
                    $startTime = $startTime->addMinutes($bookingTime->slot_duration);
                }
            } else {
                $startTime = Carbon::parse($bookingTime->start_time)->format($this->settings->time_format);

                $startTime = Carbon::parse("$date $startTime", $company->timezone)->setTimezone('UTC')->format($this->settings->date_format ." ". $this->settings->time_format);;
//                $startTime = Carbon::createFromFormat($company->time_format, $bookingTime->utc_start_time);
                $startTime = Carbon::parse($startTime, "UTC");
            }

            $endTime = Carbon::parse($bookingTime->end_time)->format($this->settings->time_format);

            $endTime = Carbon::parse("$date $endTime", $company->timezone)->setTimezone('UTC')->format($this->settings->date_format ." ". $this->settings->time_format);;
//                $startTime = Carbon::createFromFormat($company->time_format, $bookingTime->utc_start_time);
            $endTime = Carbon::parse($endTime, "UTC");

            /*$endTime = Carbon::parse($bookingTime->utc_end_time, $company->timezone)->setTimezone('UTC')->format($this->settings->time_format);
            $endTime = Carbon::parse("$date $endTime","UTC");*/

            $variables = compact('startTime', 'endTime', 'bookingTime', 'bookings', 'company');
        }

        $view = view('admin.booking.booking_slots', $variables)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * show
     *
     * @param  Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_booking') && !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        $this->booking = Booking::with([
            'users' => function ($q) {
                $q->withoutGlobalScope(CompanyScope::class);
            },
            'coupon' => function ($q) {
                $q->withoutGlobalScope(CompanyScope::class);
            },
            'user' => function ($q) {
                $q->withoutGlobalScope(CompanyScope::class);
            },
        ])->find($id);

        $this->current_url = ($request->current_url != null) ? $request->current_url : 'calendarPage';
        $this->commonCondition = $this->booking->payment_status == 'pending' && $this->booking->status != 'canceled' && $this->credentials->show_payment_options == 'show' && !$this->user->is_admin && !$this->user->is_employee;
        $this->ratings = Rating::where('booking_id', $id)->get();

        if ($request->current_url == 'calendarPage') {
            return view('admin.booking.show', $this->data);
        }

        $view = view('admin.booking.show', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {

        abort_if(!auth()->user()->roles()->withoutGlobalScopes()->first()->hasPermission(['update_booking']), 403);
        $selected_booking_user = array();
        $booking_users = Booking::with([
            'users' => function ($q) {
                $q->withoutGlobalScope(CompanyScope::class);
            }
        ])->find($id);

        foreach ($booking_users->users as $key => $user)
        {
            array_push($selected_booking_user, $user->id);
        }

        $this->selected_booking_user = $selected_booking_user;

        $this->booking = Booking::with([
            'users' => function ($q) {
                $q->withoutGlobalScope(CompanyScope::class);
            },
            'user' => function ($q) {
                $q->withoutGlobalScope(CompanyScope::class);
            },
            'deal' => function ($q) {
                $q->withoutGlobalScope(CompanyScope::class);
            },
            'deal.location' => function ($q) {
                $q->withoutGlobalScope(CompanyScope::class);
            },
            'items' => function ($q) {
                $q->withoutGlobalScope(CompanyScope::class);
            }
        ])->find($id);

        $this->tax = Tax::active()->first();
        $this->employees = User::OtherThanCustomers()->get();
        $this->businessServices = BusinessService::active()->get();
        $this->products = Product::active()->get();
        $this->current_url = $request->current_url ? $request->current_url : 'calendarPage';

        if ($request->current_url == 'bookingPage' || $request->current_url == 'customerPage') {
            $view = view('admin.booking.edit', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'view' => $view]);
        }

        return view('admin.booking.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateBooking $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBooking $request, $id)
    {
        abort_if(!auth()->user()->roles()->withoutGlobalScopes()->first()->hasPermission(['update_booking']), 403);

        try {
            DB::beginTransaction();
            /* these are product varibles */
            $products       = $request->cart_products;
            $productQty     = $request->product_quantity;
            $productPrice   = $request->product_prices;

            $types          = $request->types;
            $employees      = $request->employee_id;
            $services       = $request->item_ids;
            $quantity       = $request->cart_quantity;
            $taxPrice      = $request->tax_amount;
            $prices         = $request->item_prices;
            $discount       = $request->cart_discount;
            $payment_status = $request->payment_status;
            $discountAmount = 0;
            $originalProductAmt = 0;
            $amountToPay    = 0;

            $originalAmount = 0;
            $bookingItems   = array();
            $productTax     = 0;
            $taxPercent     = 0;
            $tax            = 0;
            $taxAmount      = 0;
            $productTaxAmt  = 0;

            /* save services and deals */
            if (!is_null($services)) {
                foreach ($services as $key => $service) {
                    $amount = ($quantity[$key] * $prices[$key]);

                    $deal_id = ($types[$key] == 'deal') ? $services[$key] : null;
                    $service_id = ($types[$key] == 'service') ? $services[$key] : null;

                    $bookingItems[] = [
                        'business_service_id' => $service_id,
                        'quantity' => $quantity[$key],
                        'unit_price' => $prices[$key],
                        'amount' => $amount,
                        'deal_id' => $deal_id,
                    ];

                    $originalAmount = ($originalAmount + $amount);

                    if ($types[$key] == 'deal') {
                        $taxes = ItemTax::with('tax')->where('deal_id', $deal_id)->get();
                    }
                    else {
                        $taxes = ItemTax::with('tax')->where('service_id', $service_id)->get();
                    }

                    $tax = 0;

                    foreach ($taxes as $key => $value) {
                        $tax += $value->tax->percent;
                        $taxName[] = $value->tax->name;
                        $taxPercent += $value->tax->percent;
                    }

                    $taxAmount += ($amount * $tax) / 100;
                }
            }

            $productItems = [];
            /* save products */
            if (!is_null($products)) {
                foreach ($products as $key => $product) {
                    $productAmt = ($productQty[$key] * $productPrice[$key]);

                    $productItems[] = [
                        'product_id' => $product,
                        'quantity' => $productQty[$key],
                        'unit_price' => $productPrice[$key],
                        'amount' => $productAmt
                    ];

                    $originalProductAmt = ($originalProductAmt + $productAmt);

                    $taxes = ItemTax::with('tax')->where('product_id', $product)->get();

                    $productTax = 0;

                    foreach ($taxes as $key => $value) {
                        $productTax += $value->tax->percent;
                        $taxName[] = $value->tax->name;
                        $taxPercent += $value->tax->percent;
                    }

                    $productTaxAmt += ($productAmt * $productTax) / 100;
                }
            }


            $totalTax = $taxAmount + $productTaxAmt;

            $amountToPay = $originalAmount;

            $booking = Booking::where('id', $id)
                ->with([
                    'payment' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    },
                    'user' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    },
                ])
                ->first();

            $taxAmount = 0;

            if ($discount > 0) {
                if ($discount > 100) {
                    $discount = 100;
                }

                $discountAmount = (($discount / 100) * $originalAmount);
                $amountToPay = ($originalAmount - $discountAmount);
            }

            $amountToPay = ($amountToPay + $totalTax);

            if (!is_null($request->coupon_id))
            {
                if ($amountToPay <= $request->coupon_amount) {
                    $amountToPay = 0;
                }
                else {
                    $amountToPay -= $request->coupon_amount;
                }
            }

            if ($originalProductAmt > 0) {
                $amountToPay = ($amountToPay + $originalProductAmt);
            }

            $amountToPay = round($amountToPay, 2);


//            $booking->date_time   = Carbon::createFromFormat('Y-m-d H:i a', $request->booking_date . ' ' . $request->hidden_booking_time)->format('Y-m-d H:i:s');
            $booking->date_time   = Carbon::parse($request->booking_date . ' ' . $request->hidden_booking_time, $booking->company->timezone)->format($booking->company->time_format);
            $booking->status      = $request->status;
            $booking->original_amount = $originalAmount;
            $booking->product_amount = $originalProductAmt;
            $booking->discount = $discountAmount;
            $booking->discount_percent = $request->cart_discount;;
            $booking->tax_amount = $totalTax;
            $booking->amount_to_pay = $amountToPay;
            $booking->payment_status = $payment_status;

            $booking->save();

            /* assign employees to this appointment */
            if (!empty($employees)) {
                $assignedEmployee   = array();

                foreach ($employees as $key => $employee) {
                    $assignedEmployee[] = $employees[$key];
                }

                $booking = Booking::with([
                    'payment' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    },
                    'user' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    },
                    'users' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    },
                ])->find($id);
                $booking->users()->sync($assignedEmployee);
            }

            // Delete old items and enter new booking_date
            BookingItem::where('booking_id', $id)->delete();

            $total_amount = 0.00;

            if (!is_null($services)) {

                foreach ($bookingItems as $key => $bookingItem) {
                    $bookingItems[$key]['booking_id'] = $booking->id;
                    $bookingItems[$key]['company_id'] = $booking->company_id;
                    $total_amount += $bookingItem['amount'];
                }

                DB::table('booking_items')->insert($bookingItems);
            }

            $total_amt = 0.00;

            if (!is_null($products)) {

                foreach ($productItems as $key => $productItem) {
                    $productItems[$key]['booking_id'] = $booking->id;
                    $productItems[$key]['company_id'] = $booking->company_id;
                    $total_amt += $productItem['amount'];
                }

                DB::table('booking_items')->insert($productItems);
            }

            if (!$booking->payment) {

                $payment = new Payment();
                $payment->currency_id = $this->settings->currency_id;
                $payment->booking_id = $booking->id;
                $payment->amount = $amountToPay;
                $payment->gateway = 'cash';
                $payment->status = $payment_status;
                $payment->paid_on = Carbon::now();
            }
            else {
                $payment = $booking->payment;
                $payment->status = $payment_status;
                $payment->amount = $amountToPay;
            }

            $payment->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort_and_log(403, $e->getMessage());
        }


        $current_url = ($request->current_url != null) ? $request->current_url : 'calendarPage';
        $commonCondition = $booking->payment_status == 'pending' && $booking->status != 'canceled' && $this->credentials->show_payment_options == 'show' && !$this->user->is_admin && !$this->user->is_employee;

        $completedBookings = Booking::where('user_id', $booking->user_id)->where('status', 'completed')->count();
        $approvedBookings = Booking::where('user_id', $booking->user_id)->where('status', 'approved')->count();
        $pendingBookings = Booking::where('user_id', $booking->user_id)->where('status', 'pending')->count();
        $canceledBookings = Booking::where('user_id', $booking->user_id)->where('status', 'canceled')->count();
        $inProgressBookings = Booking::where('user_id', $booking->user_id)->where('status', 'in progress')->count();
        $earning = Booking::where('user_id', $booking->user_id)->where('status', 'completed')->sum('amount_to_pay');
        $ratings = Rating::where('booking_id', $id)->get();

        $view = view('admin.booking.show', compact('ratings', 'booking', 'commonCondition', 'current_url'))->render();

        $customerStatsView = view('partials.customer_stats', compact('completedBookings', 'approvedBookings', 'pendingBookings', 'inProgressBookings', 'canceledBookings', 'earning'))->render();

        return Reply::successWithData('messages.updatedSuccessfully', ['status' => 'success', 'view' => $view, 'customerStatsView' => $customerStatsView]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('delete_booking'), 403);

        Booking::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

    public function download($id)
    {

        $booking = Booking::with([
            'users' => function ($q) {
                $q->withoutGlobalScope(CompanyScope::class);
            },
            'user' => function ($q) {
                $q->withoutGlobalScope(CompanyScope::class);
            },
        ])->find($id);

        abort_if($booking->status != 'completed', 403);

        if ($this->user->is_admin || $this->user->is_employee || $booking->user_id == $this->user->id) {

            $pdf = app('dompdf.wrapper');
            $pdf->loadView('admin.booking.receipt', compact('booking'));
            $filename = __('app.receipt') . ' #' . $booking->id;
            return $pdf->download($filename . '.pdf');
        }
        else {
            abort(403);
        }
    }

    public function invocePdf($id)
    {

        $booking = Booking::with([
            'users' => function ($q) {
                $q->withoutGlobalScope('company');
            },
            'user' => function ($q) {
                $q->withoutGlobalScope('company');
            },
        ])->find($id);

        abort_if($booking->status != 'completed', 403);

        if ($this->user->is_admin || $this->user->is_employee || $booking->user_id == $this->user->id) {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('admin.booking.receipt', compact('booking'));
            return $pdf->stream();
        }
        else {
            abort(403);
        }
    }

    public function print($id)
    {
        $this->id = $id;

        return view('admin.booking.print', $this->data);
    }

    public function requestCancel(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'canceled';
        $booking->save();

        $commonCondition = $booking->payment_status == 'pending' && $booking->status != 'canceled' && $this->credentials->show_payment_options == 'show' && !$this->user->is_admin && !$this->user->is_employee;
        $current_url = ($request->current_url != null) ? $request->current_url : 'calendarPage';
        $view = view('admin.booking.show', compact('booking', 'commonCondition', 'current_url'))->render();

        $admins = User::allAdministrators()->get();
        $role = $this->user->is_admin == true && $this->user->is_employee == false ? 'Admin' : 'Customer';

        Notification::send($admins, new BookingCancel($booking, $role));

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function sendReminder()
    {
        $bookingId = \request('bookingId');
        $booking = Booking::findOrFail($bookingId);

        $customer = User::withoutGlobalScopes()->findOrFail($booking->user_id);

        $customer->notify(new BookingReminder($booking));

        return Reply::success(__('messages.bookingReminderSent'));
    }

    public function multiStatusUpdate(BookingStatusMultiUpdate $request)
    {

        foreach ($request->booking_checkboxes as $key => $booking_checkbox) {
            $booking = Booking::find($booking_checkbox);
            $booking->status = $request->change_status;
            $booking->save();
        }

        return Reply::dataOnly(['status' => 'success', '']);
    }

    public function updateCoupon(Request $request)
    {
        $couponId = $request->coupon_id;

        $tax = Tax::active()->first();

        $productAmount = $request->cart_services;

        if ($request->cart_discount > 0) {
            $totalDiscount = ($request->cart_discount / 100) * $productAmount;
            $productAmount -= $totalDiscount;
        }

        $percentAmount = ($tax->percent / 100) * $productAmount;

        $totalAmount   = ($productAmount + $percentAmount);

        $currentDate = Carbon::now()->format('Y-m-d H:i:s');

        $couponData = Coupon::where('coupons.start_date_time', '<=', $currentDate)
            ->where(function ($query) use ($currentDate) {
                $query->whereNull('coupons.end_date_time')
                    ->orWhere('coupons.end_date_time', '>=', $currentDate);
            })
            ->where('coupons.id', $couponId)
            ->where('coupons.status', 'active')
            ->first();

        if (!is_null($couponData) && $couponData->minimum_purchase_amount != 0 && $couponData->minimum_purchase_amount != null && $productAmount < $couponData->minimum_purchase_amount) {
            return Reply::errorWithoutMessage();
        }

        if (!is_null($couponData) && $couponData->used_time >= $couponData->uses_limit && $couponData->uses_limit != null && $couponData->uses_limit != 0) {
            return Reply::errorWithoutMessage();
        }

        if (!is_null($couponData)) {
            $days = json_decode($couponData->days);
            $currentDay = Carbon::now()->format('l');

            if (in_array($currentDay, $days)) {

                if (!is_null($couponData->percent) && $couponData->percent != 0) {
                    $percentAmnt = round(($couponData->percent / 100) * $totalAmount, 2);

                    if (!is_null($couponData->amount) && $percentAmnt >= $couponData->amount) {
                        $percentAmnt = $couponData->amount;
                    }

                    return Reply::dataOnly(['amount' => $percentAmnt, 'couponData' => $couponData]);
                }
                elseif (!is_null($couponData->amount) && (is_null($couponData->percent) || $couponData->percent == 0)) {
                    return Reply::dataOnly(['amount' => $couponData->amount, 'couponData' => $couponData]);
                }

            } else {
                return Reply::errorWithoutMessage();
            }
        }

        return Reply::errorWithoutMessage();
    }

    public function updateBookingDate(Request $request, $id)
    {
        abort_if(!$this->user->can('update_booking'), 403);

        $booking = Booking::where('id', $id)->first();
        $booking->date_time   = Carbon::parse($request->startDate)->format('Y-m-d H:i:s');
        $booking->save();

        return Reply::successWithData('messages.updatedSuccessfully', ['status' => 'success']);
    }

    public function feedBack(Request $request, $id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('read_booking') && !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        $this->booking = Booking::with([
            'users' => function ($q) {
                $q->withoutGlobalScope(CompanyScope::class);
            },
            'coupon' => function ($q) {
                $q->withoutGlobalScope(CompanyScope::class);
            },
            'user' => function ($q) {
                $q->withoutGlobalScope(CompanyScope::class);
            },
            'ratings' => function ($q) {
                $q->where('user_id', Auth::user()->id);
            },
        ])->find($id);

        $this->current_url = ($request->current_url != null) ? $request->current_url : 'calendarPage';
        $this->commonCondition = $this->booking->payment_status == 'pending' && $this->booking->status != 'canceled' && $this->credentials->show_payment_options == 'show' && !$this->user->is_admin && !$this->user->is_employee;

        $this->ratings = Rating::with([
            'service' => function ($q) {
                $q->withoutGlobalScope('company');
            },
            'deal' => function ($q) {
                $q->withoutGlobalScope('company');
            },
            'product' => function ($q) {
                $q->withoutGlobalScope('company');
            },
        ])->where('user_id', Auth::user()->id)->where('booking_id', $id)->get();

        if ($request->current_url == 'calendarPage') {
            return view('admin.booking.feedback_modal', $this->data);
        }

        $view = view('admin.booking.feedback_modal', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function storeRating(Request $request)
    {
        $itemId = $request->itemId;
        $itemType = $request->itemType;
        $bookingId = $request->bookingId;
        $ratingValue = $request->ratingValue;
        $companyId = Booking::where('id', $bookingId)->first()->company_id;

        if ($request->ratingId == 'store') {

            foreach ($itemType as $key => $type)
            {
                $rating = new Rating();
                $rating->company_id = $companyId;
                $rating->booking_id = $bookingId;
                $rating->user_id = Auth::user()->id;

                if ($type == 'service') {
                    $rating->service_id = $itemId[$key];
                }

                if ($type == 'deal') {
                    $rating->deal_id = $itemId[$key];
                }

                if ($type == 'product') {
                    $rating->product_id = $itemId[$key];
                }

                $rating->rating = $ratingValue[$key];
                $rating->status = 'active';

                $rating->save();
            }
        }
        else {

            $rating = Rating::where('booking_id', $request->bookingId)->get();

            foreach ($rating as $ratings) {
                $ratings->delete();
            }

            foreach ($itemType as $key => $type)
            {
                $rating = new Rating();
                $rating->company_id = $companyId;
                $rating->booking_id = $bookingId;
                $rating->user_id = Auth::user()->id;

                if ($type == 'service') {
                    $rating->service_id = $itemId[$key];
                }

                if ($type == 'deal') {
                    $rating->deal_id = $itemId[$key];
                }

                if ($type == 'product') {
                    $rating->product_id = $itemId[$key];
                }

                $rating->rating = $ratingValue[$key];
                $rating->status = 'active';

                $rating->save();
            }

        }

        return Reply::dataOnly(['status' => 'success']);

    }


    public function suggestEmployee($date, $service_ids)
    {
        /* check for all employee of that service, of that particular location  */
        $dateTime = $date;

//        [$service_ids, $service_names] = Arr::divide(json_decode(request()->cookie('products'), true));

        $user_lists = BusinessService::with('users')->whereIn('id', $service_ids)->get();

        $all_users_of_particular_services = array();

        foreach ($user_lists as $user_list) {
            foreach ($user_list->users as $user) {
                $all_users_of_particular_services[] = $user->id;
            }
        }

        /* if no empolyee for that particular service is found then allow booking with null employee assignment  */
        if (empty($all_users_of_particular_services)) {
            return '';
        }

        /* Employee schedule: */
        $day = $dateTime->format('l');
        $time = $dateTime->format('H:i:s');
        $date = $dateTime->format('Y-m-d');

        /* Check for employees working on that day: */
        $employeeWorking = EmployeeSchedule::with('employee')->where('days', $day)
            ->whereTime('start_time', '<=', $time)->whereTime('end_time', '>=', $time)
            ->where('is_working', 'yes')->whereIn('employee_id', $all_users_of_particular_services)->get();

        $working_employee = array();

        foreach ($employeeWorking as $employeeWorkings) {
            $working_employee[] = $employeeWorkings->employee->id;
        }

        $assigned_user_list_array = array();
        $assigned_users_list = Booking::with('users')
            ->where('date_time', $dateTime)
            ->get();

        foreach ($assigned_users_list as $key => $value) {
            foreach ($value->users as $key1 => $value1) {
                $assigned_user_list_array[] = $value1->id;
            }
        }

        $free_employee_list = array_diff($working_employee, array_intersect($working_employee, $assigned_user_list_array));

        /* Leave: */

        /* check for half day*/
        $halfday_leave = Leave::with('employee')->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)->whereTime('start_time', '<=', $time)
            ->whereTime('end_time', '>=', $time)->where('leave_type', 'Half day')->where('status', 'approved')->get();

        $users_on_halfday_leave = array();

        foreach ($halfday_leave as $halfday_leaves) {
            $users_on_halfday_leave[] = $halfday_leaves->employee->id;
        }

        /* check for full day*/
        $fullday_leave = Leave::with('employee')->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)->where('leave_type', 'Full day')->where('status', 'approved')->get();

        $users_on_fullday_leave = array();

        foreach ($fullday_leave as $fullday_leaves) {
            $users_on_fullday_leave[] = $fullday_leaves->employee->id;
        }

        $employees_not_on_halfday_leave = array_diff($free_employee_list, array_intersect($free_employee_list, $users_on_halfday_leave));

        $employees_not_on_fullday_leave = array_diff($free_employee_list, array_intersect($free_employee_list, $users_on_fullday_leave));

        $companyId = Role::select('company_id')->where('id', auth()->user()->role->id)->first()->company_id;
        $company = Company::where('id', $companyId)->first();

        /* if any employee is on leave on that day */
        if ($this->getCartCompanyDetail()->employee_selection == 'enabled') {

            return User::allEmployees()->select('id', 'name')->whereIn('id', $employees_not_on_fullday_leave)->whereIn('id', $employees_not_on_halfday_leave)->get();

        }

        /* if no employee found then return allow booking with no employee assignment   */
        if (empty($free_employee_list)) {
            if ($this->getCartCompanyDetail()->multi_task_user == 'enabled') {
                /* give single users */
                return User::select('id', 'name')->whereIn('id', $all_users_of_particular_services)->first()->id;
            }
        }

        /* select of all remaining employees */
        $users = User::select('id', 'name')->whereIn('id', $free_employee_list);

        if ($this->settings->disable_slot == 'enabled') {

            foreach ($users->get() as $key => $employee_list) {
                // call function which will see employee schedules
                $user_schedule = $this->checkUserSchedule($employee_list->id, $date);

                if ($user_schedule == true) {
                    return $employee_list->id;
                }
            }
        }

        return $users->first()->id;
    }

} /* end of class */
