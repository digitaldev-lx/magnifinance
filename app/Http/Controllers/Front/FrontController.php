<?php

namespace App\Http\Controllers\Front;

use App\Advertise;
use App\Article;
use App\Country;
use App\Services\StripeCustomerManager;
use App\SmsSetting;
use App\Tax;
use App\Deal;
use App\Page;
use App\Role;
use App\User;
use App\Leave;
use App\Media;
use App\Coupon;
use App\Booking;
use App\Company;
use App\ItemTax;
use App\Package;
use App\Category;
use App\Currency;
use App\FrontFaq;
use App\Language;
use App\Location;
use App\Spotlight;
use Carbon\Carbon;
use App\VendorPage;
use App\BookingItem;
use App\BookingTime;
use App\OfficeLeave;
use App\Helper\Reply;
use App\GlobalSetting;
use App\BusinessService;
use App\UniversalSearch;
use App\EmployeeSchedule;
use DigitalDevLX\Magnifinance\Facades\Magnifinance;
use Illuminate\Support\Arr;
use App\Scopes\CompanyScope;
use Illuminate\Http\Request;
use App\Notifications\NewUser;
use App\Notifications\ContactUs;
use App\Notifications\NewBooking;
use App\PaymentGatewayCredentials;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Notifications\CompanyWelcome;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StoreFrontBooking;
use App\Notifications\BookingConfirmation;
use App\Http\Requests\Front\ContactRequest;
use App\Http\Requests\Front\CartPageRequest;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\FrontBaseController;
use App\Http\Requests\Company\RegisterCompany;
use App\Http\Requests\ApplyCoupon\ApplyRequest;
use App\Notifications\SuperadminNotificationAboutNewAddedCompany;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Crawler\Crawler;
use Spatie\Sitemap\SitemapGenerator;
use Stripe\Stripe;

class FrontController extends FrontBaseController
{

    public function index()
    {
        $couponData = json_decode(request()->cookie('couponData'), true);

        if ($couponData) {
            setcookie('couponData', '', time() - 3600);
        }

        if (request()->ajax()) {
            /* LOCATION */
            $location_id = request()->location_id;

            /* CATRGORIES */
            $categories = Category::active()->withoutGlobalScope(CompanyScope::class)
                ->activeCompanyService()
                ->with(['services' => function ($query) use ($location_id) {
                    $query->active()->withoutGlobalScope(CompanyScope::class)->where('location_id', $location_id);
                }])
                ->withCount(['services' => function ($query) use ($location_id) {
                    $query->withoutGlobalScope(CompanyScope::class)->where('location_id', $location_id);
                }]);

            $total_categories_count = $categories->count();
            $categories = $categories->take(8)->get();


            /* DEALS */
            $deals = Deal::withoutGlobalScope(CompanyScope::class)
                ->active()
                ->activeCompany()
                ->with(['location', 'services', 'company' => function ($q) {
                    $q->withoutGlobalScope(CompanyScope::class);
                }])
                ->where('start_date_time', '<=', Carbon::now()->setTimezone($this->settings->timezone))
                ->where('end_date_time', '>=', Carbon::now()->setTimezone($this->settings->timezone))
                ->where('location_id', $location_id);

            $total_deals_count = $deals->count();
            $deals = $deals->take(10)->get();

            $spotlight = Spotlight::with(['deal', 'company' => function ($q) {
                $q->withoutGlobalScope(CompanyScope::class);
            }])
                ->activeCompany()
                ->whereHas('deal', function ($q) use ($location_id) {
                    $q->whereHas('location', function ($q) use ($location_id) {
                        $q->where('location_id', $location_id);
                    });
                })->orderBy('sequence', 'asc')->get();

            $articles = Article::published()->withoutGlobalScope(CompanyScope::class)
                ->with([
                    'category' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    },
                ])
                ->where('status', 'approved')
                ->latest()->take(10)->get();

            return Reply::dataOnly(['articles' => $articles, 'categories' => $categories, 'total_categories_count' => $total_categories_count, 'deals' => $deals, 'total_deals_count' => $total_deals_count, 'spotlight' => $spotlight]);
        }

        /* COUPON */
        $coupons = Coupon::active();

        $this->coupons = $coupons->take(12)->get();

        $this->sliderContents = Media::all();

        return view('front.index', $this->data);
    }

    public function addOrUpdateProduct(Request $request)
    {
        if ($request->type == 'service') {
            $service = BusinessService::whereId($request->id)->first();
        }

        $newProduct = [
            'type' => $request->type,
            'unique_id' => $request->unique_id,
            'companyId' => $request->companyId,
            'price' => $request->price,
            'tax_on_price_status' => isset($service) && $service->tax_on_price_status == 'active' ? 'active' : 'inactive',
            'name' => $request->name,
            'id' => $request->id,
        ];

        $products = [];
        $tax = [];
        $quantity = $request->quantity ?? 1;

        if ($request->type == 'deal') {
            $deals = Deal::withoutGlobalScope(CompanyScope::class)->where('id', $request->id)
                ->with([
                    'dealTaxes' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    }
                ])->first();

            if (($deals->uses_limit != null) && ($deals->uses_limit <= $deals->used_time)) {
                return Reply::error(__('app.maxDealUses'));
            }

            $tax = [];

            if ($deals->dealTaxes) {
                foreach ($deals->dealTaxes as $key => $deal) {
                    $taxDetail = Tax::select('id', 'name', 'percent')->active()->where('id', $deal->tax_id)->first();
                    $tax[] = $taxDetail;
                }
            }

            $newProduct = Arr::add($newProduct, 'tax', json_encode($tax));
            $newProduct = Arr::add($newProduct, 'max_order', $request->max_order);
        }

        if ($request->type == 'service') {
            $services = BusinessService::withoutGlobalScope(CompanyScope::class)->where('id', $request->id)
                ->with([
                    'taxServices' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    }
                ])->first();

            $tax = [];

            if ($services->taxServices) {

                foreach ($services->taxServices as $key => $service) {
                    $taxDetail = Tax::select('id', 'name', 'percent')->active()->where('id', $service->tax_id)->first();
                    $tax[] = $taxDetail;
                }

            }

            $newProduct = Arr::add($newProduct, 'tax', json_encode($tax));
        }

        if ($request->type == 'deal') {
            $dealId = $request->id;

            if (Auth::check() == 'true') {
//                $deals = Booking::where('user_id', Auth::user()->id)->with(['deal'])->count();

                $deals = Booking::where('user_id', Auth::id())->with([
                    'items' => function ($q) use ($dealId) {
                        $q->where('deal_id', $dealId);
                    },
                ])->count();

                /* if type is deal and max_order_per_customer is exceeded then block increasing quantity */
                if ($request->max_order <= $deals) {
                    return Reply::error(__('app.maxDealMessage', ['quantity' => $this->checkDealQuantity($request->id)]));
                }
            }
        }

        if (!$request->hasCookie('products')) {
            $newProduct = Arr::add($newProduct, 'quantity', $quantity);
            $newProduct = Arr::add($newProduct, 'quantity', $quantity);
            $products = Arr::add($products, $request->unique_id, $newProduct);

            return response([
                'status' => 'success',
                'message' => __('messages.front.success.productAddedToCart'),
                'productsCount' => count($products)
            ])->cookie('products', json_encode($products));
        }

        $products = json_decode($request->cookie('products'), true);

        /* if type is deal and max_order_per_customer is exceeded then block increasing quantity */
        if ($request->type == 'deal' && array_key_exists($request->unique_id, $products) && $this->checkDealQuantity($request->id) !== 0 && $this->checkDealQuantity($request->id) <= $products[$request->unique_id]['quantity']) {
            return Reply::error(__('app.maxDealMessage', ['quantity' => $this->checkDealQuantity($request->id)]));
        }

        /* Checking if item belongs to some other company */
        $companyIds = [];
        $types = [];

        foreach ($products as $key => $product) {
            $companyIds[] = $product['companyId'];
            $types[] = $product['type'];
        }

        /* check if incoming service belong to same company as cart has */
        if (!in_array($request->companyId, $companyIds)) {
            return response(['result' => 'fail', 'message' => __('messages.front.errors.differentItemFound')])->cookie('products', json_encode($products));
        }

        /* Checking if item has different type then cart item */
        if (!in_array($request->type, $types)) {
            return response(['result' => 'fail', 'message' => __('messages.front.errors.addOneItemAtATime')])->cookie('products', json_encode($products));
        }

        if (!array_key_exists($request->unique_id, $products)) {
            $newProduct = Arr::add($newProduct, 'quantity', $quantity);
            $newProduct = Arr::add($newProduct, 'tax', json_encode($tax));
            $products = Arr::add($products, $request->unique_id, $newProduct);

            return response([
                'status' => 'success',
                'message' => __('messages.front.success.productAddedToCart'),
                'productsCount' => count($products)
            ])->cookie('products', json_encode($products));
        } else {
            if ($request->quantity) {
                $products[$request->unique_id]['quantity'] = $request->quantity;
            } else {
                $products[$request->unique_id]['quantity'] += 1;
            }
        }

        return response([
            'status' => 'success',
            'message' => __('messages.front.success.cartUpdated'),
            'productsCount' => count($products)
        ])->cookie('products', json_encode($products));
    }

    public function bookingPage(Request $request)
    {

        $bookingDetails = [];

        if ($request->hasCookie('bookingDetails')) {
            $bookingDetails = json_decode($request->cookie('bookingDetails'), true);
        }

        if ($request->ajax()) {
            return Reply::dataOnly(['status' => 'success', 'productsCount' => $this->productsCount]);
        }

        $locale = App::getLocale();

        return view('front.booking_page', compact('bookingDetails', 'locale'));
    }

    public function addBookingDetails(CartPageRequest $request)
    {
        $expireTime = Carbon::parse($request->bookingDate . ' ' . $request->bookingTime, $this->settings->timezone);
        $cookieTime = Carbon::now()->setTimezone($this->settings->timezone)->diffInMinutes($expireTime);

        $emp_name = '';

        if (!empty($request->selected_user)) {
            $emp_name = User::find($request->selected_user)->name;
        }

        return response(Reply::dataOnly(['status' => 'success']))->cookie('bookingDetails', json_encode(['bookingDate' => $request->bookingDate, 'bookingTime' => $request->bookingTime, 'selected_user' => $request->selected_user, 'emp_name' => $emp_name]), $cookieTime);
    }

    public function cartPage(Request $request)
    {
        $products = json_decode($request->cookie('products'), true);
        $bookingDetails = json_decode($request->cookie('bookingDetails'), true);
        $couponData = json_decode($request->cookie('couponData'), true);
        $taxes = Tax::active()->get();
        $commission = PaymentGatewayCredentials::first();
        $type = '';

        if (!is_null(json_decode($request->cookie('products'), true))) {
            $product = (array)json_decode(request()->cookie('products', true));
            $keys = array_keys($product);
            $type = $product[$keys[0]]->type == 'deal' ? 'deal' : 'booking';
        }
        return view('front.cart', compact('commission', 'products', 'taxes', 'bookingDetails', 'couponData', 'type'));
    }

    public function deleteProduct(Request $request, $id)
    {
        $products = json_decode($request->cookie('products'), true);

        if ($id != 'all') {
            Arr::forget($products, $id);
        } else {

            $productsCount = is_null($products) ? 0 : count($products);

            return response(Reply::successWithData(__('messages.front.success.cartCleared'), ['action' => 'redirect', 'url' => route('front.cartPage'), 'productsCount' => $productsCount]))
                ->withCookie(Cookie::forget('bookingDetails'))
                ->withCookie(Cookie::forget('products'))
                ->withCookie(Cookie::forget('couponData'));
        }

        if (count($products) > 0) {
            setcookie('products', '', time() - 3600);
            return response(Reply::successWithData(__('messages.front.success.productDeleted'), ['productsCount' => count($products), 'products' => $products]))->cookie('products', json_encode($products));
        }

        return response(Reply::successWithData(__('messages.front.success.cartCleared'), ['action' => 'redirect', 'url' => route('front.cartPage'), 'productsCount' => count($products)]))->withCookie(Cookie::forget('bookingDetails'))->withCookie(Cookie::forget('products'))->withCookie(Cookie::forget('couponData'));
    }

    public function updateCart(Request $request)
    {
        $product = $request->products;

        if ($request->type == 'deal' && $request->currentValue > $request->max_order) {
            $product[$request->unique_id]['quantity'] = $request->max_order;

            return response(Reply::error(__('app.maxDealMessage', ['quantity' => $request->max_order])));
        }

        return response(Reply::success(__('messages.front.success.cartUpdated')))->cookie('products', json_encode($product));
    }

    public function checkoutPage()
    {
        $products = (array)json_decode(request()->cookie('products', true));
        $keys = array_keys($products);

        $request_type = $products[$keys[0]]->type == 'deal' ? 'deal' : 'booking';

        $emp_name = '';

        if (!empty(json_decode(request()->cookie('bookingDetails'))->selected_user)) {
            $emp_name = User::find(json_decode(request()->cookie('bookingDetails'))->selected_user)->name;
        }

        $bookingDetails = request()->hasCookie('bookingDetails') ? json_decode(request()->cookie('bookingDetails'), true) : [];
        $couponData = request()->hasCookie('couponData') ? json_decode(request()->cookie('couponData'), true) : [];

        $Amt = 0;
        $tax = 0;
        $totalAmount = 0;
        $taxAmount = 0;

        if ($request_type !== 'deal') {

            foreach ($products as $key => $service) {
                $taxes = ItemTax::with('tax')->where('service_id', $service->id)->get();
                $taxPercent = 0;

                foreach ($taxes as $key => $value) {
                    $taxPercent = $value->tax->percent;
                }

                $itemAmount = $service->price * $service->quantity;
                $tax = $itemAmount * ($taxPercent / 100);

                $net_price = round($service->price / (1 + $taxPercent / 100) * $service->quantity, 2);

                $Amt += $itemAmount;
                $taxAmount += $tax;

            }

            $totalAmount = $Amt;

        } else {

            foreach ($products as $key => $deal) {
                $taxes = ItemTax::with('tax')->where('deal_id', $deal->id)->get();
                $tax = 0;


                foreach ($taxes as $key => $value) {
                    $tax += $value->tax->percent;
                }

                $Amt = $deal->price * $deal->quantity;
                $taxAmount += ($Amt * $tax) / 100;
                $totalAmount += $deal->price * $deal->quantity;
            }
        }

        /*if ($taxAmount > 0) {
            $totalAmount = $taxAmount + $totalAmount;
        }*/

        if ($couponData) {
            if ($totalAmount <= $couponData['applyAmount']) {
                $totalAmount = 0;
            } else {
                $totalAmount -= $couponData['applyAmount'];
            }
        }

        $totalAmount = round($totalAmount, 2);

        return view('front.checkout_page', compact('totalAmount', 'bookingDetails', 'request_type', 'emp_name'));
    }

    public function paymentFail(Request $request, $bookingId = null)
    {
        $credentials = PaymentGatewayCredentials::withoutGlobalScopes()->first();

        if ($bookingId == null) {
            $booking = Booking::where([
                'user_id' => $this->user->id
            ])
                ->latest()
                ->first();
        } else {
            $booking = Booking::where(['id' => $bookingId, 'user_id' => $this->user->id])->first();
        }

        $setting = Company::with('currency')->first();
        $user = $this->user;

        return view('front.payment', compact('credentials', 'booking', 'user', 'setting'));
    }

    public function paymentSuccess(Request $request, $bookingId = null)
    {

        $credentials = PaymentGatewayCredentials::withoutGlobalScopes()->first();

        if ($bookingId == null) {
            $booking = Booking::where([
                'user_id' => $this->user->id
            ])
                ->latest()
                ->first();
        } else {
            $booking = Booking::where(['id' => $bookingId, 'user_id' => $this->user->id])->first();
        }

        $setting = Company::with('currency')->first();
        $user = $this->user;

        if ($booking->payment_status !== 'completed') {
            $booking->payment_status = 'completed';
            $booking->save();
        }

        return view('front.payment', compact('credentials', 'booking', 'user', 'setting'));
    }

    public function paymentGateway(Request $request)
    {
        if (!Auth::user()) {
            return $this->logout();
        }

        $credentials = PaymentGatewayCredentials::withoutGlobalScopes()->first();

        $booking = Booking::with('deal', 'users')->where([
            'user_id' => $this->user->id
        ])
            ->latest()
            ->first();

        $emp_name = '';

        if (array_key_exists(0, $booking->users->toArray())) {
            $emp_name = $booking->users->toArray()[0]['name'];
        }

        $setting = Company::with('currency')->first();
        $globalSetting = GlobalSetting::with('currency')->first();
        $frontThemeSetting = $this->frontThemeSettings;
        $user = $this->user;

        if ($booking->payment_status == 'completed') {
            return redirect(route('front.index'));
        }

        return view('front.payment-gateway', compact('credentials', 'booking', 'user', 'setting', 'globalSetting', 'frontThemeSetting', 'emp_name'));
    }

    public function offlinePayment($bookingId = null, $return_url = null)
    {
        if ($bookingId == null) {
            $booking = Booking::where(['user_id' => $this->user->id])->latest()->first();
        } else {
            $booking = Booking::where(['id' => $bookingId, 'user_id' => $this->user->id])->first();
        }

        if (!$booking || $booking->payment_status == 'completed') {

            return redirect()->route('front.index');
        }

        $booking->payment_status = 'pending';
        $booking->save();

        $admins = User::allAdministrators()->where('company_id', $booking->company_id)->first();
        Notification::send($admins, new NewBooking($booking));
        $booking->user->notify(new BookingConfirmation($booking));

        if ($return_url != null && $return_url = 'calendarPage') {

            Session::put('success', __('messages.updatedSuccessfully'));
            return redirect()->route('admin.bookings.index');
        }

        return view('front.booking_success');
    }

    public function teste()
    {

        $company = Company::withoutGlobalScopes()->where("id", $stripeAccountId->company->id)->first();
        if((!is_null($company->vat_number) || !empty($company->vat_number)) && $company->country->iso == "PT"){
            return $partner = Magnifinance::addPartner($company);
            if($partner->IsSuccess){
                $company->magnifinance_active = 1;
                $company->save();
            }
        }
        return Magnifinance::addPartner(company());
        $plan = Package::find(company()->package_id);
        return $package;
        return round(round($plan->$package) / (1 + 23 / 100), 2, PHP_ROUND_HALF_UP);
//        return Magnifinance::getDocumentFromPartner("143373975", "239637712");
//        $advertise = Advertise::whereId(3)->first();

//        return $booking->company->country->iso == "PT" ? $booking->company->post_code : "1000-001";
//        return $plan->getMorphClass();
        return $plan->getDocument();
        return $plan->emitDocument(company());


        /*$data = array(
            "UserName" => "Paulo Serrano",
            "UserEmail" => "pauloamserrano@gmail.com",
            "UserPhone" => "961546227",
            "CompanyTaxId" => "239637712",
            "CompanyLegalName" => "Nome da Empresa",
            "CompanyAddress" => "Morada da empresa",
            "CompanyCity" => "Amadora",
            "CompanyPostCode" => "2700-744",
            "CompanyCountry" => "PT"
        );*/

        $data = [];
        /*$client = array(
            "Name" => "Paulo Serrano",
            "NIF" => "212655043",
            "Email" => "pauloamserrano@gmail.com",
            "Address" => "Morada do cliente",
            "City" => "Amadora",
            "PostCode" => "2700-744",
            "CountryCode" => "PT",
            "LegalName" => "Nome Legal",
            "PhoneNumber" => "966666666"
        );*/

        $client = array(
            "Name" => "Ana Garcia",
            "NIF" => "239637712",
            "Email" => "pauloamserrano@gmail.com",
            "Address" => "Rua dos Arneiros 21C",
            "City" => "Lisboa",
            "PostCode" => "1500-055",
            "CountryCode" => "PT",
            "LegalName" => "Centro Ana Garcia",
            "PhoneNumber" => "966666666"
        );

        $list = [
            [
                "Code" => "010", // Service or Product ID, min lenght 2
                "Description" => "Advertise from day one to day ten",
                "UnitPrice" => 40,
                "Quantity" => 1,
                "Unit" => "Service",
                "Type" => "S", // S = Service P = Product
                "TaxValue" => 23, // percentage
                "ProductDiscount" => 0, // Percentage
                "CostCenter" => "Advertises"
            ]
            ];

        $document = [
            "Type" => "T", // T = Fatura/Recibo, I = Fatura, S = Fatura Simplificada, C - Nota de Credito, D = Nota de Debito
            "Date" => "2022-03-21", // Data do Serviço format("Y-m-d")
            "DueDate" => "2022-03-21", // Data do Pagamento
            "Description" => "Descrição",
//            "Serie" => "",
//            "TaxExemptionReasonCode" => "",
            "ExternalId" => 46, //transaction Id
            "Lines" => $list
        ];
        return $document;
//todo: optimizar a criação de documento para os vários tipos de transação (anuncios, planos e compra de serviços)
        $advertise = Advertise::whereId(1)->first();
        return $document = Magnifinance::emitDocumentFromOwner($advertise, $document,"pauloamserrano@gmail.com");

        return $document = Magnifinance::emitDocumentFromOwner($advertise, $document,"pauloamserrano@gmail.com");
//            $advertise->addDocument();
//        return Magnifinance::getPartnerToken("239637712");
        return Magnifinance::getDocumentFromPartner("143373975", "239637712");
        return Magnifinance::emitDocumentFromOwner($client, $document, "pauloamserrano@gmail.com");


//        "143371309"
//        return Magnifinance::getPartnerToken(239637712);
//        return Magnifinance::addPartner($data);
    }

    public function bookingSlots(Request $request)
    {
        $company = $this->getCartCompanyDetail();

        if (!is_null($this->user) && $company->booking_per_day != (0 || '') && $company->booking_per_day <= $this->user->userBookingCount(Carbon::createFromFormat('Y-m-d', $request->bookingDate))) {
            $msg = __('messages.reachMaxBooking') . Carbon::createFromFormat('Y-m-d', $request->bookingDate)->format('Y-m-d');
            return Reply::dataOnly(['status' => 'fail', 'msg' => $msg]);
        }

        $bookingDate = Carbon::createFromFormat('Y-m-d', $request->bookingDate);
        $day = $bookingDate->format('l');
        $bookingTime = BookingTime::withoutGlobalScope(CompanyScope::class)->where('company_id', $company->id)->where('day', strtolower($day))->first();
        // Check if multiple booking allowed

        $bookings = Booking::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $company->id)
            ->whereDate('date_time', '=', $bookingDate->format('Y-m-d'));

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
            if ($bookingDate->day === Carbon::today()->day) {
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
        $view = view('front.booking_slots', $variables)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function validateGoogleReCaptcha($googleReCaptchaResponse)
    {
        $client = new Client();
        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'form_params' => [
                    'secret' => $this->googleCaptchaSettings->v2_secret_key,
                    'response' => $googleReCaptchaResponse,
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                ]
            ]
        );

        $body = json_decode((string)$response->getBody());

        return $body->success;
    }

    public function googleRecaptchaMessage()
    {
        throw ValidationException::withMessages([
            'g-recaptcha-response' => [trans('app.recaptchaFailed')],
        ]);
    }

    public function saveBooking(StoreFrontBooking $request)
    {
        $location = $request->location;
        if(isset($request->from_pos) && $request->from_pos == "true"){
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

                $unit_price = $service->net_price;

                $amount = convertedOriginalPrice($companyId, ($request->cart_quantity[$i] * $service->net_price));

                $taxAmount += $amount * ($taxPercent / 100);

                $originalAmount += $amount;

                $deal_id = null;
                $business_service_id = $service->id;

                $bookingItems[] = [
                    'business_service_id' => $business_service_id,
                    'quantity' => $request->cart_quantity[$i],
                    'unit_price' => convertedOriginalPrice($companyId, $unit_price),
                    'amount' => $amount,
                    'deal_id' => $deal_id,
                ];

            }

            $amountToPay = ($originalAmount);

            /*if ($couponData) {
                if ($amountToPay <= $couponData['applyAmount']) {
                    $amountToPay = 0;
                } else {
                    $amountToPay -= $couponData['applyAmount'];
                }

                $couponDiscountAmount = $couponData['applyAmount'];
            }*/

            $amountToPay = round($amountToPay, 2);
            $dateTime = Carbon::createFromFormat('Y-m-d', $request->date)->format('Y-m-d') . ' ' . Carbon::createFromFormat('H:i:s', $request->booking_time)->format('H:i:s');

            $currencyId = Company::withoutGlobalScope(CompanyScope::class)->find($companyId)->currency_id;

        }else{
            if ($this->user) {
                $user = $this->user;
            } else {
                // User type from email/username
                $user = User::where($this->user, $request->{$this->user})->first();

                try {
                    DB::beginTransaction();
                    $password = Str::random(8);
                    $user = User::firstOrNew(['email' => $request->email]);
                    $user->name = $request->first_name . ' ' . $request->last_name;
                    $user->email = $request->email;
                    $user->mobile = $request->phone;
                    $user->vat_number = $request->vat_number ?? '999999990';
                    $user->calling_code = $request->calling_code;
                    $user->password = \Hash::make($password);
                    $user->save();

                    $user->attachRole(Role::where('name', 'customer')->first()->id);

                    Auth::loginUsingId($user->id);
                    $this->user = $user;
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    abort_and_log(403, $e->getMessage());
                }


                if ($this->smsSettings->nexmo_status == 'active' && !$user->mobile_verified) {
                    // verify user mobile number
                    return response(Reply::redirect(route('front.checkoutPage'), __('messages.front.success.userCreated')));
                }

                $user->notify(new NewUser($password));
            }
            $source = "online";
            $products = (array)json_decode(request()->cookie('products', true));
            $keys = array_keys($products);
            $type = $products[$keys[0]]->type == 'deal' ? 'deal' : 'booking';

            // get products and bookingDetails
            $products = json_decode($request->cookie('products'), true);

            // Get Applied Coupon Details
            $couponData = request()->hasCookie('couponData') ? json_decode(request()->cookie('couponData'), true) : [];

            /* booking details having bookingDate, bookingTime, selected_user, emp_name */
            $bookingDetails = json_decode($request->cookie('bookingDetails'), true);

            if (is_null($products) && ($type != 'deal' || is_null($bookingDetails))) {
                return response(Reply::redirect(route('front.index')));
            }

            if ($type == 'booking') {
                // get bookings and bookingTime as per bookingDetails date
                $bookingDate = Carbon::createFromFormat('Y-m-d', $bookingDetails['bookingDate']);
                $day = $bookingDate->format('l');
                $bookingTime = BookingTime::where('day', strtolower($day))->first();

                $bookings = Booking::select('id', 'date_time')->where(DB::raw('DATE(date_time)'), $bookingDate->format('Y-m-d'))->whereRaw('DAYOFWEEK(date_time) = ' . ($bookingDate->dayOfWeek + 1))->get();

                if ($bookingTime->max_booking != 0 && $bookings->count() > $bookingTime->max_booking) {
                    return response(Reply::redirect(route('front.bookingPage')))->withCookie(Cookie::forget('bookingDetails'));
                }
            }

            $originalAmount = $taxAmount = $amountToPay = $discountAmount = $couponDiscountAmount = 0;

            $bookingItems = array();

            $companyId = 0;

            $tax = 0;
            $taxName = [];
            $taxPercent = 0;
            $Amt = 0;

            foreach ($products as $key => $product) {

                if ($type !== 'deal') {
                    $taxes = ItemTax::with('tax')->where('service_id', $product['id'])->get();
                } else {
                    $taxes = ItemTax::with('tax')->where('deal_id', $product['id'])->get();
                }

                $tax = 0;

                foreach ($taxes as $key => $value) {
                    $tax += $value->tax->percent;
                    $taxName[] = $value->tax->name;
                    $taxPercent += $value->tax->percent;
                }

                $companyId = $product['companyId'];

                if ($product['tax_on_price_status'] == 'active') {
                    $net_price = round($product['price'] / (1 + $tax / 100) * $product['quantity'], 2);
                    $amount = convertedOriginalPrice($companyId, ($product['quantity'] * $product['price']));

                    $Amt += ($product['price'] * $product['quantity']);
//                $Amt += $net_price;
//                $taxAmount += ($product['price'] * $product['quantity']) - $net_price;
                    $taxAmount += 0;
                } else {

                    $amount = convertedOriginalPrice($companyId, ($product['quantity'] * $product['price']));
                    $parcel = $product['price'] * $product['quantity'];
                    $Amt += $parcel;
                    $taxAmount += ($parcel * $tax) / 100;
                }

                $originalAmount += $amount;

                $deal_id = ($product['type'] == 'deal') ? $product['id'] : null;
                $business_service_id = ($product['type'] == 'service') ? $product['id'] : null;

                $bookingItems[] = [
                    'business_service_id' => $business_service_id,
                    'quantity' => $product['quantity'],
                    'unit_price' => convertedOriginalPrice($companyId, $product['price']),
                    'amount' => $amount,
                    'deal_id' => $deal_id,
                ];

            }

            $amountToPay = ($originalAmount + $taxAmount);

            if ($couponData) {
                if ($amountToPay <= $couponData['applyAmount']) {
                    $amountToPay = 0;
                } else {
                    $amountToPay -= $couponData['applyAmount'];
                }

                $couponDiscountAmount = $couponData['applyAmount'];
            }

            $amountToPay = round($amountToPay, 2);

            $dateTime = $type !== 'deal' ? Carbon::createFromFormat('Y-m-d', $bookingDetails['bookingDate'])->format('Y-m-d') . ' ' . Carbon::createFromFormat('H:i:s', $bookingDetails['bookingTime'])->format('H:i:s') : '';
            $currencyId = Company::withoutGlobalScope(CompanyScope::class)->find($companyId)->currency_id;
        }

        try {
            DB::beginTransaction();

            $booking = new Booking();
            $booking->company_id = $companyId;
            $booking->user_id = $user->id;
            $booking->currency_id = $currencyId;
            $booking->date_time = $dateTime;
            $booking->status = 'pending';
            $booking->payment_gateway = 'cash';
            $booking->original_amount = $originalAmount;
            $booking->discount = $discountAmount;
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

        if(isset($type)){
            if ($type !== 'deal') {
                /* Assign Suggested User To Booking */
                if (!empty(json_decode($request->cookie('bookingDetails'))->selected_user)) {
                    $booking->users()->attach(json_decode($request->cookie('bookingDetails'))->selected_user);
                    setcookie('selected_user', '', time() - 3600);
                } else {
                    if ($this->suggestEmployee($booking->date_time)) {
                        $booking->users()->attach($this->suggestEmployee($booking->date_time));
                        setcookie('user_id', '', time() - 3600);
                    }
                }
            }
        }else{
            if (isset($request->employee) && count($request->employee) > 0) {
                $booking->users()->attach($request->employee);
            } else {
                if ($this->suggestEmployee($booking->date_time)) {
                    $booking->users()->attach($this->suggestEmployee($booking->date_time));
                }
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

        if(isset($request->from_pos) && $request->from_pos == "true"){
            return Reply::dataOnly(['success' => true, 'msg' => __('front.bookingSuccessful')]);
        }

        return response(Reply::redirect(route('front.payment-gateway'), __('messages.front.success.bookingCreated')))
            ->withCookie(Cookie::forget('bookingDetails'))
            ->withCookie(Cookie::forget('couponData'))
            ->withCookie(Cookie::forget('products'));

    }

    public function searchServices(Request $request)
    {
        $search = strtolower($request->q);
        $route = Route::currentRouteName();

        if ($search != '') {
            $universalSearches = UniversalSearch::withoutGlobalScope(CompanyScope::class)->where('title', $search)->first();

            if ($universalSearches != null) {
                $universalSearch = UniversalSearch::withoutGlobalScope(CompanyScope::class)->findOrFail($universalSearches->id);
                $universalSearch->count += 1;
                $universalSearch->save();
            } elseif ($universalSearches == null) {
                $universalSearch = new UniversalSearch();
                $universalSearch->location_id = $request->l;
                $universalSearch->searchable_id = 'keywords';
                $universalSearch->searchable_type = 'service';
                $universalSearch->title = $search;
                $universalSearch->route_name = $route;
                $universalSearch->count = 1;
                $universalSearch->type = 'frontend';
                $universalSearch->save();
            }
        }

        $categories = Category::whereOnlyBlog('no')->get();
        $company_id = $category_id = '';

        return view('front.all_services', compact('categories', 'category_id', 'company_id'));
    }

    public function contact(ContactRequest $request)
    {
        $globalSetting = GlobalSetting::select('id', 'contact_email', 'company_name')->first();

        Notification::route('mail', $globalSetting->contact_email)
            ->notify(new ContactUs());

        return Reply::success(__('messages.front.success.emailSent'));
    }

    public function serviceDetail(Request $request, $companyId, $serviceSlug)
    {
        $service = BusinessService::where('slug', $serviceSlug)
            ->activeCompany()
            ->withoutGlobalScope(CompanyScope::class)
            ->with([
                'company' => function ($q) use ($companyId) {
                    $q->withoutGlobalScope(CompanyScope::class);
                    $q->where('id', $companyId);
                },
                'location' => function ($q) {
                    $q->withoutGlobalScope(CompanyScope::class);
                },
                'ratings' => function ($q) {
                    $q->withoutGlobalScope('company');
                    $q->active();
                },
            ])
            ->first();

        visitor()->visit($service);

        $products = json_decode($request->cookie('products'), true) ?: [];
        $reqProduct = array_filter($products, function ($product) use ($service) {
            return $product['unique_id'] == 'service' . $service->id;
        });

        if ($service) {
            return view('front.service_detail', compact('service', 'reqProduct'));
        }

        abort(404);
    }

    public function dealDetail(Request $request, $dealSlug)
    {
        $deal = Deal::withoutGlobalScope(CompanyScope::class)
            ->activeCompany()
            ->with([
                'company' => function ($q) {
                    $q->withoutGlobalScope(CompanyScope::class);
                },
                'location' => function ($q) {
                    $q->withoutGlobalScope(CompanyScope::class);
                },
            ])->where('slug', $dealSlug)->first();

        /* to show update cart and delete item */
        $products = json_decode($request->cookie('products'), true) ?: [];
        $reqProduct = array_filter($products, function ($product) use ($deal) {
            return $product['unique_id'] == 'deal' . $deal->id;
        });

        if ($deal) {
            return view('front.deal_detail', compact('deal', 'reqProduct'));
        }

        abort(404);
    }

    public function allLocations()
    {
        $locations = Location::active()->get();
        return Reply::dataOnly(['locations' => $locations]);
    }

    public function page($slug)
    {
        $page = Page::where('slug', $slug)->firstorFail();
        return view('front.page', compact('page'));
    }

    public function changeLanguage($code)
    {
        $language = Language::where('language_code', $code)->first();

        if (!$language) {
            return Reply::error(__('messages.coupon.invalidCode'));
        }

        $this->settings->locale = $code;
        $this->settings->save();


        return response(Reply::dataOnly(['message' => __('messages.languageChangedSuccessfully')]))->withCookie(cookie('localstorage_language_code', $code));
    }

    public function applyCoupon(ApplyRequest $request)
    {
        $couponCode = strtolower($request->coupon);
        $products = json_decode($request->cookie('products'), true);
        $tax = Tax::active()->first();
        $couponCompanyIds = [];
        $productAmount = 0;

        if (!$products) {
            return Reply::error(__('messages.coupon.addProduct'));
        }

        foreach ($products as $product) {
            $productAmount += $product['price'] * $product['quantity'];
            $couponCompanyIds[] = $product['companyId'];
        }

        /* check if coupon code exist. */
        if (is_null($couponCompanyIds) && $couponCompanyIds == null) {
            return Reply::error(__('messages.coupon.invalidCode'));
        }

        if ($tax == null) {
            $percentAmount = 0;
        } else {
            $percentAmount = ($tax->percent / 100) * $productAmount;
        }

        $totalAmount = ($productAmount + $percentAmount);

        $currentDate = Carbon::now()->format('Y-m-d H:i:s');

        $couponData = Coupon::where('coupons.start_date_time', '<=', $currentDate)
            ->where(function ($query) use ($currentDate) {
                $query->whereNull('coupons.end_date_time')
                    ->orWhere('coupons.end_date_time', '>=', $currentDate);
            })
            ->where('coupons.status', 'active')
            ->where('coupons.code', $couponCode)
            ->first();

        if (!is_null($couponData) && $couponData->minimum_purchase_amount != 0 && $couponData->minimum_purchase_amount != null && $productAmount < $couponData->minimum_purchase_amount) {
            return Reply::error(__('messages.coupon.minimumAmount') . ' ' . currencyFormatter($couponData->minimum_purchase_amount));
        }

        if (!is_null($couponData) && $couponData->used_time >= $couponData->uses_limit && $couponData->uses_limit != null && $couponData->uses_limit != 0) {
            return Reply::error(__('messages.coupon.usedMaximun'));
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

                    return response(Reply::dataOnly(['amount' => $percentAmnt, 'couponData' => $couponData]))->cookie('couponData', json_encode([$couponData, 'applyAmount' => $percentAmnt]));
                } elseif (!is_null($couponData->amount) && $couponData->amount !== 0 && $couponData->discount_type === 'amount') {
                    return response(Reply::dataOnly(['amount' => $couponData->amount, 'couponData' => $couponData]))->cookie('couponData', json_encode([$couponData, 'applyAmount' => $couponData->amount]));
                }
            } else {
                return response(
                    Reply::error(__(
                        'messages.coupon.notValidToday',
                        ['day' => __('app.' . strtolower($currentDay))]
                    ))
                );
            }
        }

        return Reply::error(__('messages.coupon.notMatched'));
    }

    public function updateCoupon(Request $request)
    {
        $couponTitle = strtolower($request->coupon);
        $products = json_decode($request->cookie('products'), true);
        $tax = Tax::active()->first();

        $productAmount = 0;

        foreach ($products as $product) {
            $productAmount += $product['price'] * $product['quantity'];
        }

        $percentAmount = ($tax->percent / 100) * $productAmount;
        $totalAmount = ($productAmount + $percentAmount);

        $currentDate = Carbon::now()->format('Y-m-d H:i:s');

        $couponData = Coupon::where('coupons.start_date_time', '<=', $currentDate)
            ->where(function ($query) use ($currentDate) {
                $query->whereNull('coupons.end_date_time')
                    ->orWhere('coupons.end_date_time', '>=', $currentDate);
            })
            ->where('coupons.status', 'active')
            ->where('coupons.title', $couponTitle)
            ->first();

        if (!is_null($couponData) && $couponData->minimum_purchase_amount != 0 && $couponData->minimum_purchase_amount != null && $productAmount < $couponData->minimum_purchase_amount) {
            return Reply::errorWithoutMessage();
        }

        if (!is_null($couponData) && $couponData->used_time >= $couponData->uses_limit && $couponData->uses_limit != null && $couponData->uses_limit != 0) {
            return Reply::errorWithoutMessage();
        }

        if (!is_null($couponData) && $productAmount > 0) {
            $days = json_decode($couponData->days);
            $currentDay = Carbon::now()->format('l');

            if (in_array($currentDay, $days)) {

                if (!is_null($couponData->percent) && $couponData->percent != 0) {
                    $percentAmnt = round(($couponData->percent / 100) * $totalAmount, 2);

                    if (!is_null($couponData->amount) && $percentAmnt >= $couponData->amount) {
                        $percentAmnt = $couponData->amount;
                    }

                    return response(Reply::dataOnly(['amount' => $percentAmnt, 'couponData' => $couponData]))->cookie('couponData', json_encode([$couponData, 'applyAmount' => $percentAmnt]));
                } elseif (!is_null($couponData->amount) && (is_null($couponData->percent) || $couponData->percent == 0)) {
                    return response(Reply::dataOnly(['amount' => $couponData->amount, 'couponData' => $couponData]))->cookie('couponData', json_encode([$couponData, 'applyAmount' => $couponData->amount]));
                }
            } else {
                return Reply::errorWithoutMessage();
            }
        }

        return Reply::errorWithoutMessage();
    }

    public function removeCoupon(Request $request)
    {
        return response(Reply::dataOnly([]))->withCookie(Cookie::forget('couponData'));
    }

    public function checkUserAvailability(Request $request)
    {

        $companyId = $this->getCartCompanyDetail()->id;

        /* check for all employee of that service, of that particular location  */
        $dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->date, $this->settings->timezone)->setTimezone('UTC');

        [$service_ids, $service_names] = Arr::divide(json_decode($request->cookie('products'), true));

        $user_lists = BusinessService::with('users')->where('company_id', $companyId)->whereIn('id', $service_ids)->get();

        $all_users_of_particular_services = array();

        foreach ($user_lists as $user_list) {
            foreach ($user_list->users as $user) {
                $all_users_of_particular_services[] = $user->id;
            }
        }


        /* Employee schedule: */
        $day = $dateTime->format('l');
        $time = $dateTime->format('H:i:s');
        $date = $dateTime->format('Y-m-d');
        $bookingTime = BookingTime::where('day', strtolower($day))->first();
        $slot_select = $date . ' ' . $time;


        $booking_slot = DB::table('bookings')->whereBetween('date_time', [$slot_select, $dateTime->addMinutes($bookingTime->slot_duration)])
            ->get();


        /* Maximum Number of Booking Allowed Per Slot check */
        if ($bookingTime->per_slot_max_booking != (0 || '') && $bookingTime->per_slot_max_booking <= $booking_slot->count()) {

            return response(Reply::dataOnly(['status' => 'fail']));
        }

        /* if no employee for that particular service is found then allow booking with null employee assignment  */
        if (empty($all_users_of_particular_services)) {
            return response(Reply::dataOnly(['continue_booking' => 'yes']));
        }

        /* Check for employees working on that day: */
        $employeeWorking = EmployeeSchedule::with('employee')->where('company_id', $companyId)->where('days', $day)
            ->whereTime('start_time', '<=', $time)->whereTime('end_time', '>=', $time)
            ->where('is_working', 'yes')->whereIn('employee_id', $all_users_of_particular_services)->get();


        $working_employee = array();

        foreach ($employeeWorking as $employeeWorkings) {
            $working_employee[] = $employeeWorkings->employee->id;
        }


        $assigned_user_list_array = array();
        $assigned_users_list = Booking::with('users')->where('company_id', $companyId)
            ->where('date_time', $dateTime)
            ->get();

        foreach ($assigned_users_list as $key => $value) {
            foreach ($value->users as $key1 => $value1) {
                $assigned_user_list_array[] = $value1->id;
            }
        }

        $free_employee_list = array_diff($working_employee, array_intersect($working_employee, $assigned_user_list_array));

        $select_user = '<select id="selected_user" name="selected_user" class="form-control mt-3"><option value="">--Select Employee--</option>';

        /* Leave: */

        /* check for half day */
        $halfday_leave = Leave::with('employee')->where('company_id', $companyId)->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)->whereTime('start_time', '<=', $time)
            ->whereTime('end_time', '>=', $time)->where('leave_type', 'Half day')->where('status', 'approved')->get();

        $users_on_halfday_leave = array();

        foreach ($halfday_leave as $halfday_leaves) {
            $users_on_halfday_leave[] = $halfday_leaves->employee->id;
        }

        /* check for full day */
        $fullday_leave = Leave::with('employee')->where('company_id', $companyId)->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)->where('leave_type', 'Full day')->where('status', 'approved')->get();

        $users_on_fullday_leave = array();

        foreach ($fullday_leave as $fullday_leaves) {
            $users_on_fullday_leave[] = $fullday_leaves->employee->id;
        }

        $employees_not_on_halfday_leave = array_diff($free_employee_list, array_intersect($free_employee_list, $users_on_halfday_leave));

        $employees_not_on_fullday_leave = array_diff($free_employee_list, array_intersect($free_employee_list, $users_on_fullday_leave));

        /* if any employee is on leave on that day */
        $employee_lists = User::allEmployees()->where('company_id', $companyId)->select('id', 'name')->whereIn('id', $free_employee_list)->get();

        $employee = User::allEmployees()->where('company_id', $companyId)->select('id', 'name')->whereIn('id', $employees_not_on_fullday_leave)->whereIn('id', $employees_not_on_halfday_leave)->get();

        if ($this->getCartCompanyDetail()->employee_selection == 'enabled') {
            $i = 0;

            foreach ($employee_lists as $employee_list) {
                $user_schedule = $this->checkUserSchedule($employee_list->id, $request->date);

                if ($this->getCartCompanyDetail()->disable_slot == 'enabled') {
                    foreach ($employee as $key => $employees) {

                        if ($user_schedule == true) {
                            $select_user .= '<option value="' . $employees->id . '">' . $employees->name . '</option>';
                            $i++;
                            $select_user .= '</select>';
                        }

                        if ($i > 0) {
                            return response(Reply::dataOnly(['continue_booking' => 'yes', 'select_user' => $select_user]));
                        }

                        return response(Reply::dataOnly(['continue_booking' => 'no']));
                    }
                } else {
                    foreach ($employee as $key => $employees) {
                        $select_user .= '<option value="' . $employees->id . '">' . $employees->name . '</option>';
                    }

                    $select_user .= '</select>';
                    return response(Reply::dataOnly(['continue_booking' => 'yes', 'select_user' => $select_user]));
                }
            }
        }

        /* if no employee found of that particular service */

        if (empty($free_employee_list)) {

            if ($this->getCartCompanyDetail()->multi_task_user == 'enabled') {
                /* give dropdown of all users */

                if ($this->getCartCompanyDetail()->employee_selection == 'enabled') {
                    $employee_lists = User::allEmployees()->select('id', 'name')->whereIn('id', $all_users_of_particular_services)->get();

                    foreach ($employee_lists as $key => $employee_list) {
                        $select_user .= '<option value="' . $employee_list->id . '">' . $employee_list->name . '</option>';
                    }

                    $select_user .= '</select>';
                    return response(Reply::dataOnly(['continue_booking' => 'yes', 'select_user' => $select_user]));
                }
            } else {
                /* block booking here  */
                return response(Reply::dataOnly(['continue_booking' => 'no']));
            }
        }

        /* if multitasking and allow employee selection is enabled */
        if ($this->getCartCompanyDetail()->multi_task_user == 'enabled') {
            /* give dropdown of all users */
            if ($this->getCartCompanyDetail()->employee_selection == 'enabled') {
                $employee_lists = User::allEmployees()->select('id', 'name')->whereIn('id', $all_users_of_particular_services)->get();

                foreach ($employee_lists as $key => $employee_list) {
                    $select_user .= '<option value="' . $employee_list->id . '">' . $employee_list->name . '</option>';
                }

                $select_user .= '</select>';
                return response(Reply::dataOnly(['continue_booking' => 'yes', 'select_user' => $select_user]));
            }
        }

        /* select of all remaining employees */
        $employee_lists = User::allEmployees()->select('id', 'name')->whereIn('id', $free_employee_list)->get();

        if ($this->getCartCompanyDetail()->employee_selection == 'enabled') {
            $i = 0;

            foreach ($employee_lists as $key => $employee_list) {
                $user_schedule = $this->checkUserSchedule($employee_list->id, $request->date);

                if ($this->getCartCompanyDetail()->disable_slot == 'enabled') {
                    // call function which will see employee schedules
                    if ($user_schedule == true) {
                        $select_user .= '<option value="' . $employee_list->id . '">' . $employee_list->name . '</option>';
                        $i++;
                    }
                } else {
                    if ($user_schedule == true) {
                        $select_user .= '<option value="' . $employee_list->id . '">' . $employee_list->name . '</option>';
                        $i++;
                    }
                }
            }

            $select_user .= '</select>';

            if ($i > 0) {
                return response(Reply::dataOnly(['continue_booking' => 'yes', 'select_user' => $select_user]));
            }

            return response(Reply::dataOnly(['continue_booking' => 'no']));
        }

        $user_check_array = array();

        foreach ($employee_lists as $key => $employee_list) {
            // call function which will see employee schedules
            $user_schedule = $this->checkUserSchedule($employee_list->id, $request->date);

            if ($user_schedule == true) {
                $user_check_array[] = $employee_list->id;
            }
        }

        if (empty($user_check_array)) {
            return response(Reply::dataOnly(['continue_booking' => 'no']));
        }
    }

    public function checkUserSchedule($userid, $dateTime)
    {
        $new_booking_start_time = Carbon::parse($dateTime)->format('Y-m-d H:i');
        $time = $this->calculateCartItemTime();
        $end_time1 = Carbon::parse($dateTime)->addMinutes($time - 1);

        $userBooking = Booking::whereIn('status', ['pending', 'in progress', 'approved'])->with('users')->whereHas('users', function ($q) use ($userid) {
            $q->where('user_id', $userid);
        });
        $bookings = $userBooking->get();

        if ($userBooking->count() > 0) {
            foreach ($bookings as $key => $booking) {
                /* previous booking start date and time */
                $start_time = Carbon::parse($booking->date_time)->format('Y-m-d H:i');
                $booking_time = $this->calculateBookingTime($booking->id);
                $end_time = $booking->date_time->addMinutes($booking_time - 1);

                if (Carbon::parse($new_booking_start_time)->between($start_time, Carbon::parse($end_time)->format('Y-m-d H:i'), true) || Carbon::parse($start_time)->between($new_booking_start_time, Carbon::parse($end_time1)->format('Y-m-d H:i'), true)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function calculateBookingTime($booking_id)
    {
        $booking_time_type = $this->getCartCompanyDetail()->booking_time_type;
        $booking_items = BookingItem::with('businessService')->where('booking_id', $booking_id)->get();
        $time = 0;
        $total_time = 0;
        $max = 0;
        $min = 0;

        foreach ($booking_items as $key => $item) {
            if ($item->businessService->time_type == 'minutes') {
                $time = $item->businessService->time;
            } elseif ($item->businessService->time_type == 'hours') {
                $time = $item->businessService->time * 60;
            } elseif ($item->businessService->time_type == 'days') {
                $time = $item->businessService->time * 24 * 60;
            }

            $total_time += $time;

            if ($key == 0) {
                $min = $time;
                $max = $time;
            }

            if ($time < $min) {
                $min = $time;
            }

            if ($time > $max) {
                $max = $time;
            }
        }

        if ($booking_time_type == 'sum') {
            return $total_time;
        } elseif ($booking_time_type == 'max') {
            return $max;
        } elseif ($booking_time_type == 'min') {
            return $min;
        } elseif ($booking_time_type == 'avg') {
            return $total_time / $booking_items->count();
        }
    }

    public function calculateCartItemTime()
    {
        $booking_time_type = $this->getCartCompanyDetail()->booking_time_type;

        $products = json_decode(request()->cookie('products'), true);

        $bookingIds = [];

        foreach ($products as $key => $product) {
            $bookingIds[] = $key;
        }

        $booking_items = BusinessService::whereIn('id', $bookingIds)->get();

        $time = 0;
        $total_time = 0;
        $max = 0;
        $min = 0;

        foreach ($booking_items as $key => $booking_item) {

            if ($booking_item->time_type == 'minutes') {
                $time = $booking_item->time;
            } elseif ($booking_item->time_type == 'hours') {
                $time = $booking_item->time * 60;
            } elseif ($booking_item->time_type == 'days') {
                $time = $booking_item->time * 24 * 60;
            }

            $total_time += $time;

            if ($key == 0) {
                $min = $time;
                $max = $time;
            }

            if ($time < $min) {
                $min = $time;
            }

            if ($time > $max) {
                $max = $time;
            }
        }

        if ($booking_time_type == 'sum') {
            return $total_time;
        } elseif ($booking_time_type == 'max') {
            return $max;
        } elseif ($booking_time_type == 'min') {
            return $min;
        } elseif ($booking_time_type == 'avg') {
            return $total_time / $booking_items->count();
        }
    }

    public function grabDeal(Request $request)
    {
        $deal = [
            'dealId' => $request->dealId,
            'dealPrice' => $request->dealPrice,
            'dealName' => $request->dealName,
            'dealQuantity' => $request->dealQuantity,
            'dealUnitPrice' => $request->dealUnitPrice,
            'dealCompanyName' => $request->dealCompanyName,
            'dealMaxQuantity' => $request->dealMaxQuantity,
            'dealCompanyId' => $request->dealCompanyId,
        ];

        return response([
            'status' => 'success',
            'message' => 'deal added successfully',
        ])->cookie('deal', json_encode($deal));
    }

    public function suggestEmployee($date)
    {
        /* check for all employee of that service, of that particular location  */
        $dateTime = $date;

        [$service_ids, $service_names] = Arr::divide(json_decode(request()->cookie('products'), true));

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

    public function articleDetail(Request $request, $slug)
    {

        $article = Article::withoutGlobalScope(CompanyScope::class)->whereSlug($slug)
            ->with([
                'category' => function ($q) {
                    $q->withoutGlobalScope(CompanyScope::class);
                },
            ])->first();

        visitor()->visit($article);

        $service = BusinessService::find(3);

        if ($request->ajax()) {
            $advertises = Advertise::paid()
                ->where(function ($q) use ($request) {
                    $q->where('location_id', $request->location);
                    $q->orWhere('location_id', null);
                })
                ->where(function ($q) use ($article) {
                    $q->whereHas('category', function ($q) use ($article) {
                        $q->where('id', $article->category_id);
                    });
                    $q->orWhereHas('article', function ($q) use ($article) {
                        $q->where('id', $article->id);
                    });
                })->orderByDesc('amount')->orderByDesc('avg_amount')->get();

            $professionals = BusinessService::where('location_id', $request->location)
                ->whereHas('category', function ($q) use ($article) {
                    $q->where('id', $article->category_id);
                })
                ->with(['ratings', 'company', 'category'])->withCount(['ratings as average_rating' => function ($query) {
                    $query->select(DB::raw('coalesce(avg(rating),0)'));
                }])->orderByDesc('average_rating')
                ->take(12)->get();


            $articles = Article::published()->withoutGlobalScope(CompanyScope::class)
                ->with([
                    'category' => function ($q) use ($article) {
                        $q->withoutGlobalScope(CompanyScope::class);
                        $q->where('id', '=', $article->category_id);
                    },
                ])->where('id', '<>', $article->id)
                ->latest()->take(8)->get();


            $location = $this->locations->filter(function ($item) use ($request) {
                return $item->id == $request->location;
            })->first();

            $view = view('front.filtered_professionals', compact('professionals', 'article'))->render();
            $viewAds = view('front.filtered_advertises', compact('advertises'))->render();

            return Reply::dataOnly(['view' => $view, 'viewAds' => $viewAds, 'articles' => $articles, 'article' => $article, 'location' => $location, 'professionals' => $professionals, 'advertises' => $advertises]);
        }

        return view('front.article_detail', compact('article', 'service'));
    }


    public function allArticles(Request $request)
    {

        if ($request->ajax()) {
            $articles = Article::published()->withoutGlobalScope(CompanyScope::class)
                ->with([
                    'category' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    },
                ])
                ->where('status', 'approved');

            if (!is_null($request->categories)) {
                $categories = explode(',', $request->categories);
                $articles->WhereHas('category', function ($query) use ($categories) {
                    $query->WhereIn('id', $categories);
                });
            }

            if (!is_null($request->companies)) {
                $companies = explode(',', $request->companies);
                $articles->orWhereIn('company_id', $companies);
            }

            $articles = $articles->paginate(10);

            $view = view('front.filtered_articles', compact('articles'))->render();
            return Reply::dataOnly(['view' => $view, 'deal_count' => $articles->count(), 'deal_total' => $articles->total()]);
        }

        $companies = Company::withoutGlobalScope(CompanyScope::class)->get();
        $categories = Category::withoutGlobalScope(CompanyScope::class)->has('articles', '>', 0)->get();
        return view('front.all_articles', compact('categories', 'companies'));
    }

    public function allDeals(Request $request)
    {

        if ($request->ajax()) {
            $deals = Deal::active()->withoutGlobalScope(CompanyScope::class)
                ->activeCompany()
                ->with([
                    'company' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    },
                    'location' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    },
                    'services' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    },
                ]);

            if (!is_null($request->locations)) {
                $locations = explode(',', $request->locations);
                $deals->WhereHas('location', function ($query) use ($locations) {
                    $query->WhereIn('id', $locations);
                });
            }

            if (!is_null($request->categories)) {
                $categories = explode(',', $request->categories);
                $deals->WhereHas('services.businessService.category', function ($query) use ($categories) {
                    $query->WhereIn('id', $categories);
                });
            }

            if (!is_null($request->companies)) {
                $companies = explode(',', $request->companies);
                $deals->WhereIn('company_id', $companies);
            }

            if (!is_null($request->price)) {
                $prices = $request->price;

                $firstPrice = explode('-', array_shift($prices));
                $low = $firstPrice[0];
                $high = $firstPrice[1];

                $priceArr = [];

                foreach ($prices as $price) {
                    $priceArr[] = [
                        explode('-', $price)[0],
                        explode('-', $price)[1],
                    ];
                }

                $deals = $deals->whereBetween('deal_amount', [$low, $high]);

                foreach ($priceArr as $price) {
                    $deals = $deals->orWhereBetween('deal_amount', [$price[0], $price[1]]);
                }
            }

            if (!is_null($request->discounts)) {
                $discounts = $request->discounts;

                $firstDiscount = explode('-', array_shift($discounts));
                $low = $firstDiscount[0];
                $high = $firstDiscount[1];

                $discountArr = [];

                foreach ($discounts as $discount) {
                    $discountArr[] = [
                        explode('-', $discount)[0],
                        explode('-', $discount)[1],
                    ];
                }

                $deals = $deals->where('discount_type', 'percentage')->whereBetween('percentage', [$low, $high]);

                foreach ($discountArr as $discount) {

                    $deals = $deals->where('discount_type', 'percentage')->orWhereBetween('percentage', [$discount[0], $discount[1]]);
                }
            }

            if (!is_null($request->sort_by)) {
                if ($request->sort_by == 'newest') {
                    $deals->orderBy('id', 'DESC');
                } elseif ($request->sort_by == 'low_to_high') {
                    $deals->orderBy('deal_amount');
                } elseif ($request->sort_by == 'high_to_low') {
                    $deals->orderBy('deal_amount', 'DESC');
                }
            }

            $deals = $deals->paginate(10);

            $view = view('front.filtered_deals', compact('deals'))->render();
            return Reply::dataOnly(['view' => $view, 'deal_count' => $deals->count(), 'deal_total' => $deals->total()]);
        }

        $companies = Company::withoutGlobalScope(CompanyScope::class)->get();
        $categories = Category::withoutGlobalScope(CompanyScope::class)->has('services', '>', 0)->get();
        $locations = Location::withoutGlobalScope(CompanyScope::class)->active()->get();
        return view('front.all_deals', compact('locations', 'categories', 'companies'));
    }

    public function allServices(Request $request)
    {

        if ($request->ajax()) {
            $services = BusinessService::withoutGlobalScope(CompanyScope::class)
                ->activeCompany()
                ->with([
                    'location' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    },
                    'category' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    },
                    'company' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    },
                    'ratings' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                        $q->active();
                    },
                ])->active();

            if (!is_null($request->service_name)) {
                $services = $services->where('name', 'like', '%' . $request->service_name . '%');
            }

            if (is_null($request->company_id) && !is_null($request->term)) {
                $services = $services->where('name', 'like', '%' . $request->term . '%');
            }

            if (!is_null($request->company_id)) {
                $company_id = $request->company_id;
                $services = $services->whereHas('company', function ($q) use ($company_id) {
                    $q->where('id', $company_id);
                });
            }

            if (!is_null($request->locations)) {
                $locations = explode(',', $request->locations);
                $services->whereIn('location_id', $locations);
            }

            if (!is_null($request->categories)) {
                $categories = explode(',', $request->categories);
                $services->whereIn('category_id', $categories);

                $advertises = Advertise::paid()
                    ->where(function ($q) use ($request) {
                        $q->where('location_id', $request->location);
                        $q->orWhere('location_id', null);
                    })
                    ->where(function ($q) use ($categories) {
                        $q->whereHas('category', function ($q) use ($categories) {
                            $q->whereIn('id', $categories);
                        });
                    })->orderByDesc('amount')->orderByDesc('avg_amount')->get();

                $professionals = BusinessService::where('location_id', $request->location)
                    ->whereHas('category', function ($q) use ($categories) {
                        $q->whereIn('id', $categories);
                    })
                    ->with(['ratings', 'company', 'category'])->withCount(['ratings as average_rating' => function ($query) {
                        $query->select(DB::raw('coalesce(avg(rating),0)'));
                    }])->orderByDesc('average_rating')
                    ->take(12)->get();

            } else {
                $advertises = Advertise::paid()
                    ->where(function ($q) use ($request) {
                        $q->where('location_id', $request->location);
                        $q->orWhere('location_id', null);
                    })
                    ->orderByDesc('amount')->orderByDesc('avg_amount')->get();

                $professionals = BusinessService::where('location_id', $request->location)
                    ->with(['ratings', 'company', 'category'])->withCount(['ratings as average_rating' => function ($query) {
                        $query->select(DB::raw('coalesce(avg(rating),0)'));
                    }])->orderByDesc('average_rating')
                    ->take(12)->get();
            }

            if (!is_null($request->companies)) {
                $companies = explode(',', $request->companies);
                $services->whereIn('company_id', $companies);
            }

            if (!is_null($request->price)) {
                $prices = $request->price;

                $firstPrice = explode('-', array_shift($prices));
                $low = $firstPrice[0];
                $high = $firstPrice[1];

                $priceArr = [];

                foreach ($prices as $price) {
                    $priceArr[] = [
                        explode('-', $price)[0],
                        explode('-', $price)[1],
                    ];
                }

                $services = $services->whereBetween('price', [$low, $high]);

                foreach ($priceArr as $price) {
                    $services = $services->orWhereBetween('price', [$price[0], $price[1]]);
                }
            }

            if (!is_null($request->discounts)) {
                $discounts = $request->discounts;

                $firstDiscount = explode('-', array_shift($discounts));
                $low = $firstDiscount[0];
                $high = $firstDiscount[1];

                $discountArr = [];

                foreach ($discounts as $discount) {
                    $discountArr[] = [
                        explode('-', $discount)[0],
                        explode('-', $discount)[1],
                    ];
                }

                $services = $services->where('discount_type', 'percent')->whereBetween('discount', [$low, $high]);

                foreach ($discountArr as $discount) {
                    $services = $services->where('discount_type', 'percent')->orWhereBetween('discount', [$discount[0], $discount[1]]);
                }
            }

            if (!is_null($request->sort_by)) {
                if ($request->sort_by == 'newest') {
                    $services->orderBy('id', 'DESC');
                } elseif ($request->sort_by == 'low_to_high') {
                    $services->orderBy('net_price');
                } elseif ($request->sort_by == 'high_to_low') {
                    $services->orderBy('net_price', 'DESC');
                }
            }

            $services = $services->paginate(10);

            $location = $this->locations->filter(function ($item) use ($request) {
                return $item->id == $request->location;
            })->first();

            $viewProfessionals = view('front.filtered_professionals', compact('professionals'))->render();
            $viewAds = view('front.filtered_advertises', compact('advertises'))->render();

            $view = view('front.filtered_services', compact('services'))->render();
            return Reply::dataOnly(['view' => $view, 'viewAds' => $viewAds, 'viewProfessionals' => $viewProfessionals, 'service_count' => $services->count(), 'service_total' => $services->total(), 'location' => $location]);

        }

        /* end of ajax */

        $company_id = !is_null($request->company_id) ? $request->company_id : '';

        $category_id = '';

        if ($request->category_id && $request->category_id != 'all') {
            $category_id = Category::where('slug', $request->category_id)->first();

            if (!$category_id) {
                abort(404);
            }

            $category_id = $category_id->id;
        }

        $categories = Category::withoutGlobalScope(CompanyScope::class)->has('services', '>', 0)->withCount(['services' => function ($q) {
            $q->withoutGlobalScope(CompanyScope::class);
        }])
            ->get();

        return view('front.all_services', compact('categories', 'category_id', 'company_id'));
    }

    public function allCoupons(Request $request)
    {
        $coupons = Coupon::withoutGlobalScope(CompanyScope::class)
            ->with(['company' => function ($q) {
                $q->withoutGlobalScope(CompanyScope::class);
            }
            ]);

        if ($request->ajax()) {
            if (!is_null($request->companies)) {
                $companies = explode(',', $request->companies);
                $coupons->WhereIn('company_id', $companies);
            }

            if (!is_null($request->discounts)) {
                $price = explode('-', $request->discounts[0]);
                $low = $price[0];
                $high = $price[1];
                $coupons->whereBetween('percent', array($low, $high));
            }

            if (!is_null($request->sort_by)) {
                if ($request->sort_by == 'newest') {
                    $coupons->orderBy('id', 'DESC');
                } elseif ($request->sort_by == 'low_to_high') {
                    $coupons->orderBy('percent');
                } elseif ($request->sort_by == 'high_to_low') {
                    $coupons->orderBy('percent', 'DESC');
                }
            }

            $coupons = $coupons->paginate(10);
            $view = view('front.filtered_coupons', compact('coupons'))->render();
            return Reply::dataOnly(['view' => $view, 'coupon_total' => $coupons->total(), 'coupon_count' => $coupons->count()]);
        }

        $companies = Company::withoutGlobalScope(CompanyScope::class)->get();
        $coupons = $coupons->paginate(10);
        return view('front.all_coupons', compact('coupons', 'companies'));
    }

    public function getCouponCompany($code)
    {
        $coupon = Coupon::where('code', $code)->first();
        return !is_null($coupon) ? $coupon->company_id : null;
    }

    /* return all the detail of company added to cart */
    public function getCartCompanyDetail()
    {
        $products = json_decode(request()->cookie('products'), true);

        $companyIds = [];

        foreach ($products as $key => $product) {
            $companyIds[] = $product['companyId'];
        }

        if (count($companyIds) > 0) {
            return Company::where('id', $companyIds[0])->first();
        }

        return null;
    }

    public function globalSearch(Request $request)
    {

        $search = $request->term;
        $location = !is_null($request->location) ? $request->location : '';
        $filterItem = [];

        $categories = Category::where('name', 'LIKE', '%' . $search . '%')->orderBy('id', 'DESC')->limit(2)->get();

        $services = BusinessService::withoutGlobalScope(CompanyScope::class)
            ->activeCompany()
            ->with([
                'location' => function ($q) {
                    $q->withoutGlobalScope(CompanyScope::class);
                }
            ])
            ->Where('location_id', $location)
            ->where('name', 'LIKE', '%' . $search . '%')
            ->orderBy('id', 'DESC')
            ->limit(2)->get();

        $deals = Deal::withoutGlobalScope(CompanyScope::class)
            ->activeCompany()
            ->with([
                'location' => function ($q) {
                    $q->withoutGlobalScope(CompanyScope::class);
                }
            ])
            ->WhereHas('location', function ($query) use ($location) {
                $query->Where('id', $location);
            })
            ->where('title', 'LIKE', '%' . $search . '%')
            ->orderBy('id', 'DESC')
            ->limit(2)->get();


        $companies = Company::withoutGlobalScope(CompanyScope::class)
            ->active()
            ->where('company_name', 'LIKE', '%' . $search . '%')
            ->orderBy('id', 'DESC')
            ->limit(2)->get();

        if (!$categories->isEmpty()) {
            foreach ($categories as $category) {
                $filteredRes['title'] = $category->name;
                $filteredRes['image'] = $category->category_image_url;
                $filteredRes['url'] = url($category->slug . '/services');
                $filteredRes['category'] = 'Category';
                $filterItem[] = $filteredRes;
            }
        }

        if (!$services->isEmpty()) {
            foreach ($services as $service) {
                $filteredRes['title'] = $service->name;
                $filteredRes['image'] = $service->service_image_url;
                $filteredRes['url'] = $service->service_detail_url;
                $filteredRes['category'] = 'Service';
                $filterItem[] = $filteredRes;
            }
        }

        if (!$deals->isEmpty()) {
            foreach ($deals as $deal) {
                $filteredRes['title'] = $deal->title;
                $filteredRes['image'] = $deal->deal_image_url;
                $filteredRes['url'] = $deal->deal_detail_url;
                $filteredRes['category'] = 'Deal';
                $filterItem[] = $filteredRes;
            }
        }

        if (!$companies->isEmpty()) {
            foreach ($companies as $company) {
                $filteredRes['title'] = $company->company_name;
                $filteredRes['image'] = $company->logo_url;
                $filteredRes['url'] = route('front.search', ['c' => $company->id]);
                $filteredRes['category'] = 'Company';
                $filterItem[] = $filteredRes;
            }
        }

        return json_encode($filterItem);
    }

    public function register()
    {
        return view('front.register');
    }

    public function email()
    {
        return view('front.email_verification');
    }

    public function storeCompany(RegisterCompany $request)
    {
        if (request()->ajax()) {
            try {
// Check google recaptcha if setting is enabled
                if ($this->googleCaptchaSettings->status == 'active' && $this->googleCaptchaSettings->v2_status == 'active') {
                    // Checking is google recaptcha is valid
                    $gReCaptchaResponseInput = 'g-recaptcha-response';
                    $gReCaptchaResponse = $request->{$gReCaptchaResponseInput};
                    $validateRecaptcha = $this->validateGoogleReCaptcha($gReCaptchaResponse);

                    if (!$validateRecaptcha) {
                        return $this->googleRecaptchaMessage();

                    }
                }
//                $country = Country::whereId($request->country_id)->firstOrFail();

                $data = [
                    'company_name' => mb_convert_encoding($request->business_name, 'UTF-8', 'UTF-8'),
                    'company_email' => mb_convert_encoding($request->email, 'UTF-8', 'UTF-8'),
                    'company_phone' => mb_convert_encoding($request->contact, 'UTF-8', 'UTF-8'),
                    'vat_number' => mb_convert_encoding($request->vat_number, 'UTF-8', 'UTF-8'),
                    'address' => mb_convert_encoding($request->address, 'UTF-8', 'UTF-8'),
                    'post_code' => mb_convert_encoding($request->post_code, 'UTF-8', 'UTF-8'),
                    'city' => mb_convert_encoding($request->city, 'UTF-8', 'UTF-8'),
                    'country_id' => mb_convert_encoding($request->country_id, 'UTF-8', 'UTF-8'),
                    'website' => mb_convert_encoding($request->website, 'UTF-8', 'UTF-8'),
                    'date_format' => 'Y-m-d',
                    'time_format' => 'h:i A',
                    'timezone' => 'Europe/Lisbon',
                    'currency_id' => Currency::first()->id,
                    'locale' => Language::first()->language_code,
                ];

                DB::beginTransaction();
                    $country = Country::find($request->country_id);
                    $location = Location::updateOrCreate(
                        [
                            'name' => Str::title($request->city) . ', ' . Str::title($country->name),
                        ],
                        [
                            'country_id' => $request->country_id
                        ]
                    );

                    $company = Company::create($data);

                    // create admin/employee
                    $user = User::create([
                        'name' => mb_convert_encoding($request->name, 'UTF-8', 'UTF-8'),
                        'email' => mb_convert_encoding($request->email, 'UTF-8', 'UTF-8'),
                        'password' => mb_convert_encoding($request->password, 'UTF-8', 'UTF-8'),
                        'company_id' => mb_convert_encoding($company->id, 'UTF-8', 'UTF-8'),
                        'country_id' => mb_convert_encoding($request->country_id, 'UTF-8', 'UTF-8')
                    ]);
                    $user->attachRole(Role::withoutGlobalScope(CompanyScope::class)->select('id', 'name')->where(['name' => 'administrator', 'company_id' => $company->id])->first()->id);

//                    Magnifinance::addPartner($company);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => $e->getMessage()]);
            }

            return Reply::success(__('email.verificationLinkSent'));
        }

    }

    public function confirmEmail(Request $request)
    {

        $company = Company::where(['company_email' => Crypt::decryptString($request->email), 'verified' => 'no', 'status' => 'inactive'])->firstOrFail();
        $company->verified = 'yes';
        $company->status = 'active';
        $company->save();

        $company = User::withoutGlobalScopes()->where('email', '=', Crypt::decryptString($request->email))->with('company')->first();

        $superadmins = User::notCustomer()->withoutGlobalScopes()->whereNull('company_id')->get();

        // send welcome email to admin
        $company->notify(new CompanyWelcome());

        // send email to superadmin
        Notification::send($superadmins, new SuperadminNotificationAboutNewAddedCompany($company));

        return view('front/email_verified_success');
    }

    public function pricing()
    {
        $frontFaqsCount = FrontFaq::select('id', 'language_id')->where('language_id', $this->localeLanguage ? $this->localeLanguage->id : null)->count();

        $frontFaqs = FrontFaq::where('language_id', $frontFaqsCount > 0 ? ($this->localeLanguage ? $this->localeLanguage->id : null) : null)->get();

        $packages = Package::where('type', null)->get();
        return view('front.pricing', compact('packages', 'frontFaqs'));
    }

    public function checkDealQuantity($dealId)
    {
        $deal = Deal::find($dealId);
        $max_order_per_customer = !is_null($deal->max_order_per_customer) ? $deal->max_order_per_customer : 0;

        return $max_order_per_customer;
    }

    public function logout()
    {
        Auth::logout();
        return redirect('login');
    }

    public function vendorPage(Request $request, $slug)
    {
        $this->company = Company::withoutGlobalScope(CompanyScope::class)->whereSlug($slug)
            ->active()->verified()->firstOrFail();
        $this->vendorPage = VendorPage::withoutGlobalScope(CompanyScope::class)->where('company_id', $this->company->id)->first();
        $this->bookingTimes = BookingTime::withoutGlobalScope(CompanyScope::class)->where('company_id', $this->company->id)->get();
        $this->categories = Category::withoutGlobalScope(CompanyScope::class)->has('services', '>', 0)->withCount(['services' => function ($q) {
            $q->withoutGlobalScope(CompanyScope::class);
        }])
            ->get();
        visitor()->visit($this->vendorPage);
        if (!is_null($this->vendorPage->lat_long)) {
            $this->lat_long = [
                'latitude' => $this->vendorPage->lat_long->getLat(),
                'longitude' => $this->vendorPage->lat_long->getLng()
            ];
        } else {
            $this->lat_long = [
                'latitude' => 38.752100015409326,
                'longitude' => -9.200870017148958
            ];
        }
        view()->share('lat_long', $this->lat_long);

        return view('front.vendor', $this->data);
    }

    public function allCompanyDeals(Request $request, $slug)
    {
        $company = Company::withoutGlobalScope(CompanyScope::class)->whereSlug($slug)->firstOrFail();

        if ($request->ajax()) {
            $this->deals = Deal::withoutGlobalScope(CompanyScope::class)->where('company_id', $company->id)
                ->with([
                    'company' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    },
                    'location' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    },
                    'services' => function ($q) {
                        $q->withoutGlobalScope(CompanyScope::class);
                    },
                ])->paginate(10);
            $view = view('front.vendor_deals', $this->data)->render();

            return Reply::dataOnly(['view' => $view]);
        }
    }

} /* End of class */
