<?php

namespace App\Http\Controllers\Admin;

use App\Tax;
use App\User;
use App\Leave;
use App\Coupon;
use App\Booking;
use App\ItemTax;
use App\Payment;
use App\Product;
use App\Category;
use App\Location;
use Carbon\Carbon;
use App\BookingItem;
use App\Helper\Reply;
use App\BusinessService;
use App\EmployeeSchedule;
use App\Scopes\CompanyScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Pos\StorePos;
use App\Http\Controllers\AdminBaseController;

class POSController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.pos'));

        $this->middleware(function ($request, $next) {

            if(!in_array('POS', $this->user->modules)){
                abort(403);
            }

            return $next($request);
        });
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

        return view('admin.pos.create', compact('services', 'categories', 'locations', 'taxes', 'tax', 'employees'));
    }

    public function showCheckoutModal($amount)
    {
        $this->amount = $amount;
        return view('admin.pos.checkout_modal', $this->data);
    }

    /**
     * @param StorePos $request
     * @return array
     */
    public function store(StorePos $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        $dateTime      = Carbon::createFromFormat('Y-m-d H:i a', $request->pos_date.' '.$request->pos_time)->format('Y-m-d H:i:s');

        // edited at is newer than created at

        $products           = $request->product_cart_services;
        $productQty         = $request->product_cart_quantity;
        $productPrice       = $request->product_cart_prices;
        $services           = $request->cart_services;
        $quantity           = $request->cart_quantity;
        $prices             = $request->cart_prices;
        $discount           = $request->cart_discount;
        $taxAmount          = 0;
        $productTaxAmt      = 0;
        $discountAmount     = 0;
        $amountToPay        = 0;
        $originalAmount     = 0;
        $originalProductAmt = 0;
        $bookingItems       = array();
        $productItems       = array();
        $employees          = $request->employee;
        $tax                = 0;
        $productTax         = 0;
        $taxName            = [];
        $taxPercent         = 0;

        if(!is_null($services)){
            foreach ($services as $key => $service){
                $amount = ($quantity[$key] * $prices[$key]);

                $bookingItems[] = [
                    'business_service_id' => $service,
                    'quantity' => $quantity[$key],
                    'unit_price' => $prices[$key],
                    'amount' => $amount
                ];

                $originalAmount = ($originalAmount + $amount);

                $taxes = ItemTax::with('tax')->where('service_id', $service)->get();

                foreach ($taxes as $key => $value) {
                    $tax += $value->tax->percent;
                    $taxName[] = $value->tax->name;
                    $taxPercent += $value->tax->percent;
                }

                $taxAmount += ($amount * $tax) / 100;
            }
        }

        if(!is_null($products)){

            foreach ($products as $key => $product){

                $productAmt = ($productQty[$key] * $productPrice[$key]);
                $productItems[] = [
                    'product_id' => $product,
                    'quantity' => $productQty[$key],
                    'unit_price' => $productPrice[$key],
                    'amount' => $productAmt
                ];

                $originalProductAmt = ($originalProductAmt + $productAmt);

                $taxes = ItemTax::with('tax')->where('product_id', $product)->get();

                foreach ($taxes as $key => $value) {
                    $productTax += $value->tax->percent;
                    $taxName[] = $value->tax->name;
                    $taxPercent += $value->tax->percent;
                }

                $productTaxAmt += ($productAmt * $productTax) / 100;
            }
        }

        $totalTax = $taxAmount + $productTaxAmt;

        if($discount > 0){
            if($discount > 100) { $discount = 100;
            }

            $discountAmount = (($discount / 100) * $originalAmount);
        }

        $amountToPay = ($originalAmount - $discountAmount);

        $amountToPay = ($amountToPay + $totalTax);

        if (!is_null($request->coupon_id)) {
            if ($amountToPay <= $request->coupon_amount) {
                $amountToPay = 0;
            }
            else {
                $amountToPay -= $request->coupon_amount;
            }
        }

        if($originalProductAmt > 0){
            $amountToPay = ($amountToPay + $originalProductAmt);
        }

        $amountToPay = round($amountToPay, 2);

        $booking = new Booking();
        $booking->user_id          = $request->user_id;
        $booking->date_time        = $dateTime;
        $booking->location_id      = $request->location;
        $booking->status           = 'approved';
        $booking->payment_gateway  = $request->payment_gateway;
        $booking->original_amount  = $originalAmount;
        $booking->product_amount   = $originalProductAmt;
        $booking->discount         = $discountAmount;
        $booking->discount_percent = $request->cart_discount;
        $booking->payment_status   = 'completed';

        if(!is_null($tax)) {
            $booking->tax_name = json_encode($taxName);
            $booking->tax_percent = $taxPercent;
            $booking->tax_amount = $totalTax;
        }

        // Coupon Details added
        if (!is_null($request->coupon_id)) {
            $booking->coupon_id       = $request->coupon_id;
            $booking->coupon_discount = $request->coupon_amount;

            $coupon = Coupon::findOrFail($request->coupon_id);
            $coupon->used_time = ($coupon->used_time + 1);
            $coupon->save();
        }

        $booking->amount_to_pay = $amountToPay;
        $booking->save();


        if($bookingItems){

            foreach ($bookingItems as $key => $bookingItem) {
                $bookingItems[$key]['booking_id'] = $booking->id;
                $bookingItems[$key]['company_id'] = $booking->company_id;
            }

            DB::table('booking_items')->insert($bookingItems);
        }

        if($productItems){

            foreach ($productItems as $key => $productItem){
                $productItems[$key]['booking_id'] = $booking->id;
                $productItems[$key]['company_id'] = $booking->company_id;
            }

            DB::table('booking_items')->insert($productItems);
        }

        /* assign employees to this appointment */
        if($employees)
        {
            $assignedEmployee   = array();

            foreach ($employees as $key => $employee)
            {
                $assignedEmployee[] = $employees[$key];
            }

            $booking->users()->attach($assignedEmployee);
        }

        // create payment
        $payment = new Payment();

        $payment->currency_id = $this->settings->currency_id;
        $payment->booking_id  = $booking->id;
        $payment->amount      = $booking->amount_to_pay;
        $payment->gateway     = $booking->payment_gateway;
        $payment->status      = $booking->payment_status;
        $payment->paid_on     = $booking->utc_date_time;

        $payment->save();

        return Reply::redirect(route('admin.bookings.index'), __('messages.createdSuccessfully'));

    }

    public function selectCustomer()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        return view('admin.pos.select_customer');
    }

    public function searchCustomer(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        $searchTerm = $request->q;
        $users = User::withoutGlobalScopes()->has('customerBookings')->where('name', 'like', $searchTerm.'%')
            ->orWhere('mobile', 'like', '%'.$searchTerm.'%')
            ->orWhere('email', 'like', '%'.$searchTerm.'%')
            ->get();

        $items = [];

        foreach ($users as $user){
            $items[] = ['id' => $user->id, 'full_name' => $user->name, 'email' => $user->email, 'mobile' => $user->formatted_mobile];
        }

        $json = [
            'total_count' => count($users),
            'incomplete_results' => false,
            'items' => $items
        ];

        return json_encode($json);
    }

    public function filterServices(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        if ($request->category_id !== '0') {
            $categories = Category::where('id', $request->category_id)
                ->active()
                ->with([
                    'services' => function($query) use($request) {
                        if ($request->location_id !== '0') {
                            $query->active()->where('location_id', $request->location_id)->where(function($query) use ($request){ $query->where('name', 'like', '%' . $request->search_key . '%');
                            });
                        }
                        else {
                            $query->active()->where(function($query) use ($request){ $query->where('name', 'like', '%' . $request->search_key . '%');
                            });
                        }
                    }
                ])
                ->withoutGlobalScope(CompanyScope::class)
                ->get();
        }
        else {
            $categories = Category::active()
                ->with([
                    'services' => function($query) use($request) {
                        if ($request->location_id !== '0') {
                            $query->active()->where('location_id', $request->location_id)->where(function($query) use ($request){ $query->where('name', 'like', '%' . $request->search_key . '%');
                            });
                        }
                        else {
                            $query->active()->where(function($query) use ($request){ $query->where('name', 'like', '%' . $request->search_key . '%');
                            });
                        }
                    }
                ])
                ->withoutGlobalScope(CompanyScope::class)
                ->get();
        }

        $view = view('admin.pos.filtered_services', compact('categories'))->render();

        return Reply::dataOnly(['view' => $view]);
    }

    public function filterProducts(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        if ($request->type_id === '1' && $request->location_id !== '0') {

            $products = Product::active()->Where('location_id', $request->location_id)->where(function($query) use ($request){ $query->where('name', 'like', '%' . $request->search_key . '%');
            })->get();
        }
        else {

            $products = Product::active()->where(function($query) use ($request){ $query->where('name', 'like', '%' . $request->search_key . '%');
            })->get();
        }

        $view = view('admin.pos.filtered_products', compact('products'))->render();

        return Reply::dataOnly(['view' => $view]);
    }

    public function checkAvailability(Request $request)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'), 403);

        $dateTime = Carbon::createFromFormat('Y-m-d H:i a', $request->date.' '.$request->time)->format('Y-m-d H:i:s');

        $dateTimes = Carbon::createFromFormat('Y-m-d H:i:s', $dateTime, $this->settings->timezone)->setTimezone('UTC');

        $services = $request->cart_services_data;

        $user_lists = BusinessService::with('users')->whereIn('id', $services)->get();

        $all_users_of_particular_services = array();

        foreach($user_lists as $user_list) {
            foreach($user_list->users as $user) {
                $all_users_of_particular_services[] = $user->id;
            }
        }

        /* if no employee for that particular service is found then allow booking with null employee assignment  */
        if(empty($all_users_of_particular_services)) {
            return response(Reply::dataOnly(['continue_booking' => 'no']));
        }

        /* Employee schedule: */
        $day = $dateTimes->format('l');
        $time = $dateTimes->format('H:i:s');
        $date = $dateTimes->format('Y-m-d');

        /* Check for employees working on that day: */
        $employeeWorking = EmployeeSchedule::with('employee')->where('days', $day)
            ->whereTime('start_time', '<=', $time)->whereTime('end_time', '>=', $time)
            ->where('is_working', 'yes')->whereIn('employee_id', $all_users_of_particular_services)->get();
        $working_employee = array();

        foreach($employeeWorking as $employeeWorkings) {
                $working_employee[] = $employeeWorkings->employee->id;
        }

        $assigned_user_list_array = array();
        $assigned_users_list = Booking::with('users')
            ->where('date_time', $dateTimes)
            ->get();

        foreach ($assigned_users_list as $key => $value) {
            foreach ($value->users as $key1 => $value1) {
                $assigned_user_list_array[] = $value1->id;
            }
        }

        $free_employee_list = array_diff($working_employee, array_intersect($working_employee, $assigned_user_list_array));

        /* Leave: */
        /* check for half day */
        $halfDay_leave = Leave::with('employee')->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)->whereTime('start_time', '<=', $time)
            ->whereTime('end_time', '>=', $time)->where('leave_type', 'Half day')->where('status', 'approved')->get();

        $users_on_halfDay_leave = array();

        foreach($halfDay_leave as $halfDay_leaves) {
                $users_on_halfDay_leave[] = $halfDay_leaves->employee->id;
        }

        /* check for full day */
        $fullDay_leave = Leave::with('employee')->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)->where('leave_type', 'Full day')->where('status', 'approved')->get();

        $users_on_fullDay_leave = array();

        foreach($fullDay_leave as $fullDay_leaves) {
                $users_on_fullDay_leave[] = $fullDay_leaves->employee->id;
        }

        $employees_not_on_halfDay_leave = array_diff($free_employee_list, array_intersect($free_employee_list, $users_on_halfDay_leave));

        $employees_not_on_fullDay_leave = array_diff($free_employee_list, array_intersect($free_employee_list, $users_on_fullDay_leave));

        /* if any employee is on leave on that day */
        $employee_lists = User::allEmployees()->select('id', 'name')->whereIn('id', $free_employee_list)->get();

        $employee = User::allEmployees()->select('id', 'name')->whereIn('id', $employees_not_on_fullDay_leave)->whereIn('id', $employees_not_on_halfDay_leave)->get();

        if($this->settings->employee_selection == 'enabled') {

            foreach($employee_lists as $employee_list){

                $user_schedule = $this->checkUserSchedule($employee_list->id, $dateTime, $services);

                if($this->settings->disable_slot == 'enabled') {

                    if($user_schedule == true) {

                        return response(Reply::dataOnly(['continue_booking' => 'yes', 'availableEmp' => $employee]));
                    }

                    return response(Reply::dataOnly(['continue_booking' => 'no']));
                }
                else {

                    return response(Reply::dataOnly(['continue_booking' => 'yes', 'availableEmp' => $employee]));
                }
            }
        }
        else {
            /* block booking here  */
            return response(Reply::dataOnly(['continue_booking' => 'no']));
        }

        /* if no employee found of that particular service */
        if(empty($free_employee_list)) {
            if($this->settings->multi_task_user == 'enabled') {
                /* give list of all users */
                if($this->settings->employee_selection == 'enabled') {
                    $employee_lists = User::allEmployees()->select('id', 'name')->whereIn('id', $all_users_of_particular_services)->get();

                    return response(Reply::dataOnly(['continue_booking' => 'yes', 'availableEmp' => $employee_lists]));
                }
            }
            else {
                /* block booking here  */
                return response(Reply::dataOnly(['continue_booking' => 'no']));
            }
        }

        /* if multitasking and allow employee selection is enabled */
        if($this->settings->multi_task_user == 'enabled') {
            /* give list of all users */
            if($this->settings->employee_selection == 'enabled') {
                $employee_lists = User::allEmployees()->select('id', 'name')->whereIn('id', $all_users_of_particular_services)->get();

                return response(Reply::dataOnly(['continue_booking' => 'yes', 'availableEmp' => $employee_lists]));
            }
        }

    }

    public function checkUserSchedule($userId, $dateTime, $services)
    {
        $new_booking_start_time = Carbon::parse($dateTime)->format('Y-m-d H:i');
        $time = $this->calculateCartItemTime($services);
        $end_time1 = Carbon::parse($dateTime)->addMinutes($time - 1);

        $userBooking = Booking::whereIn('status', ['pending','in progress', 'approved'])->with('users')->whereHas('users', function($q)use($userId){
            $q->where('user_id', $userId);
        });
        $bookings = $userBooking->get();

        if($userBooking->count() > 0) {
            foreach ($bookings as $key => $booking) {
                /* previous booking start date and time */
                $start_time = Carbon::parse($booking->date_time)->format('Y-m-d H:i');
                $booking_time = $this->calculateBookingTime($booking->id);
                $end_time = $booking->date_time->addMinutes($booking_time - 1);

                if( Carbon::parse($new_booking_start_time)->between($start_time, Carbon::parse($end_time)->format('Y-m-d H:i'), true) || Carbon::parse($start_time)->between($new_booking_start_time, Carbon::parse($end_time1)->format('Y-m-d H:i'), true) ) {
                    return false;
                }
            }
        }

        return true;
    }

    public function calculateBookingTime($booking_id)
    {
        $booking_items = BookingItem::with('businessService')->where('booking_id', $booking_id)->get();
        $time = 0; $total_time = 0; $max = 0; $min = 0;

        foreach ($booking_items as $key => $item) {

            if($item->businessService->time_type == 'minutes') { $time = $item->businessService->time;
            }
            elseif($item->businessService->time_type == 'hours') { $time = $item->businessService->time * 60;
            }
            elseif($item->businessService->time_type == 'days') { $time = $item->businessService->time * 24 * 60;
            }

            $total_time += $time;

            if($key == 0) { $min = $time; $max = $time;
            }

            if($time < $min) { $min = $time;
            }

            if($time > $max) { $max = $time;
            }
        }

        if($this->settings->booking_time_type == 'sum') {
            return $total_time;
        }
        elseif($this->settings->booking_time_type == 'avg') {
            return $total_time / $booking_items->count();
        }
        elseif($this->settings->booking_time_type == 'max') {
            return $max;
        }
        elseif($this->settings->booking_time_type == 'min') {
            return $min;
        }
    }

    public function calculateCartItemTime($services)
    {
        $bookingIds = [];

        foreach ($services as $key => $product) {
            $bookingIds[] = $product;
        }

        $booking_items = BusinessService::whereIn('id', $bookingIds)->get();
        $time = 0; $total_time = 0; $max = 0; $min = 0;

        foreach($booking_items as $key => $booking_item) {

            if($booking_item->time_type == 'minutes') { $time = $booking_item->time;
            }
            elseif($booking_item->time_type == 'hours') { $time = $booking_item->time * 60;
            }
            elseif($booking_item->time_type == 'days') { $time = $booking_item->time * 24 * 60;
            }

            $total_time += $time;

            if($key == 0) { $min = $time; $max = $time;
            }

            if($time < $min) { $min = $time;
            }

            if($time > $max) { $max = $time;
            }
        }

        if($this->settings->booking_time_type == 'sum'){ return $total_time;
        }
        elseif($this->settings->booking_time_type == 'avg'){ return $total_time / $booking_items->count();
        }
        elseif($this->settings->booking_time_type == 'max'){ return $max;
        }
        elseif($this->settings->booking_time_type == 'min'){ return $min;
        }
    }

    /**
     * @param Request $request
     * @return $this|array|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function applyCoupon(Request $request)
    {
        $couponTitle = strtolower($request->coupon);
        $products    = $request->cart_services;
        $tax         = Tax::active()->first();

        $productAmount = 0;

        foreach ($products as $key => $product)
        {
            $productData = BusinessService::findOrFail($product[0]);

            if($productData->discount_type == 'percent'){
                $percentPrice = ($productData->discount / 100) * $productData->price;
            }
            else{
                $percentPrice = $productData->discount;
            }

            $productAmount += ($productData->price - $percentPrice) * $product[1];
        }


        if($request->cart_discount > 0){
            $totalDiscount = ($request->cart_discount / 100) * $productAmount;
            $discountProductAmount = ($productAmount - $totalDiscount);
        }
        else{
            $discountProductAmount = $productAmount;
        }

        $percentAmount = !is_null($tax) && $tax->percent > 0 ? (($tax->percent / 100) * $discountProductAmount) : 0;
        $totalAmount   = ($discountProductAmount + $percentAmount);

        $currentDate = Carbon::now()->format('Y-m-d H:i:s');

        $couponData = Coupon::where('start_date_time', '<=', $currentDate)
            ->where(function ($query) use($currentDate) {
                $query->whereNull('end_date_time')
                    ->orWhere('end_date_time', '>=', $currentDate);
            })
            ->where('code', $request->coupon)
            ->where('status', 'active')
            ->first();

        if (!is_null($couponData) && $couponData->minimum_purchase_amount != 0 && $couponData->minimum_purchase_amount != null && $productAmount < $couponData->minimum_purchase_amount)
        {
            return Reply::error(__('messages.coupon.minimumAmount').' '.currencyFormatter($couponData->minimum_purchase_amount, myCurrencySymbol()));
        }

        if (!is_null($couponData) && $couponData->used_time >= $couponData->uses_limit && $couponData->uses_limit != null && $couponData->uses_limit != 0) {
            return Reply::error(__('messages.coupon.usedMaximum'));
        }

        if (!is_null($couponData)) {
            $days = json_decode($couponData->days);
            $currentDay = Carbon::now()->format('l');

            if (in_array($currentDay, $days)) {

                if (!is_null($couponData->amount) && $couponData->amount !== 0 && $couponData->discount_type === 'percentage') {
                    $percentAmnt = round(($couponData->amount / 100) * $totalAmount, 2);

                    if (!is_null($couponData->amount) && $percentAmnt >= $couponData->amount) {
                        $percentAmnt = $couponData->amount;
                    }

                    return response(Reply::successWithData(__('messages.coupon.couponApplied'), ['amount' => $percentAmnt, 'couponData' => $couponData]));
                }
                elseif (!is_null($couponData->amount) && $couponData->amount !== 0 && $couponData->discount_type === 'amount') {
                    return response(Reply::successWithData(__('messages.coupon.couponApplied'), ['amount' => $couponData->amount, 'couponData' => $couponData]));
                }
            }
            else {
                return response(Reply::error(__('messages.coupon.notMatched')));
            }
        }

        return Reply::error(__('messages.coupon.notMatched'));

    }

    /**
     * @param Request $request
     * @return $this|array|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function updateCoupon(Request $request)
    {
        $couponTitle = strtolower($request->coupon);
        $products    = $request->cart_services;
        $tax         = Tax::active()->first();

        $productAmount = 0;

        foreach ($products as $key => $product)
        {
            $productData = BusinessService::findOrFail($product[0]);

            if($productData->discount_type == 'percent'){
                $percentPrice = ($productData->discount / 100) * $productData->price;
            }
            else{
                $percentPrice = $productData->discount;
            }

            $productAmount += ($productData->price - $percentPrice) * $product[1];
        }

        if($request->cart_discount > 0){
            $totalDiscount = ($request->cart_discount / 100) * $productAmount;
            $discountProductAmount = ($productAmount - $totalDiscount);
        }
        else{
            $discountProductAmount = $productAmount;
        }

        $percentAmount = ($tax->percent / 100) * $discountProductAmount;

        $totalAmount   = ($discountProductAmount + $percentAmount);

        $currentDate = Carbon::now()->format('Y-m-d H:i:s');

        $couponData = Coupon::where('coupons.start_date_time', '<=', $currentDate)
            ->where(function ($query) use($currentDate) {
                $query->whereNull('coupons.end_date_time')
                    ->orWhere('coupons.end_date_time', '>=', $currentDate);
            })
            ->where('coupons.title', $couponTitle)
            ->where('coupons.status', 'active')
            ->first();

        if (!is_null($couponData) && $couponData->minimum_purchase_amount != 0 && $couponData->minimum_purchase_amount != null && $productAmount < $couponData->minimum_purchase_amount)
        {
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

                    return response(Reply::dataOnly( ['amount' => $percentAmnt, 'couponData' => $couponData]));
                }
                elseif (!is_null($couponData->amount) && (is_null($couponData->percent) || $couponData->percent == 0)) {
                    return response(Reply::dataOnly(['amount' => $couponData->amount, 'couponData' => $couponData]));
                }

            }
            else {
                return Reply::errorWithoutMessage();
            }
        }

        return Reply::errorWithoutMessage();
    }

}
