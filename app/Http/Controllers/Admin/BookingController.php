<?php

namespace App\Http\Controllers\Admin;

use App\BookingTime;
use App\Category;
use App\Country;
use App\OfficeLeave;
use App\Tax;
use App\User;
use App\Coupon;
use App\Rating;
use App\Booking;
use App\Company;
use App\ItemTax;
use App\Payment;
use App\Product;
use App\Location;
use Carbon\Carbon;
use App\BookingItem;
use App\Helper\Reply;
use App\BusinessService;
use App\Scopes\CompanyScope;
use Illuminate\Http\Request;
use App\PaymentGatewayCredentials;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Notifications\BookingCancel;
use Illuminate\Support\Facades\Auth;
use App\Notifications\BookingReminder;
use App\Http\Requests\Booking\UpdateBooking;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\BookingStatusMultiUpdate;

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

        $bookingDetails = [];

        if (request()->hasCookie('bookingDetails')) {
            $bookingDetails = json_decode(request()->cookie('bookingDetails'), true);
        }

        if (request()->ajax()) {
            return Reply::dataOnly(['status' => 'success', 'productsCount' => $this->productsCount]);
        }

        $locale = App::getLocale();

        return view('admin.booking.create', compact('services', 'categories', 'locations', 'taxes', 'tax', 'employees', 'bookingDetails', 'locale'));
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
            if ($bookingDate->format("Y-m-d") === Carbon::today()->format("Y-m-d")) {
                $startTime = Carbon::createFromFormat($this->settings->time_format, $bookingTime->utc_start_time);

                while ($startTime->lessThan(Carbon::now())) {
                    $startTime = $startTime->addMinutes($bookingTime->slot_duration);
                }
            } else {
//                $startTime = Carbon::createFromFormat($this->settings->time_format, $bookingTime->utc_start_time);
                $startTime = Carbon::createFromFormat($company->time_format, $bookingTime->utc_start_time);
            }

            $endTime = Carbon::createFromFormat($company->time_format, $bookingTime->utc_end_time);
            $startTime->setTimezone($company->timezone);
            $endTime->setTimezone($company->timezone);

            $startTime->setDate($bookingDate->year, $bookingDate->month, $bookingDate->day);
            $endTime->setDate($bookingDate->year, $bookingDate->month, $bookingDate->day);

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
        abort_if(!$this->user->can('update_booking'), 403);

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
        abort_if(!$this->user->can('update_booking'), 403);

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

            $booking->date_time   = Carbon::createFromFormat('Y-m-d H:i a', $request->booking_date . ' ' . $request->hidden_booking_time)->format('Y-m-d H:i:s');
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

} /* end of class */
