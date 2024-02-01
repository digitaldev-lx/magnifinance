<?php

namespace App\Http\Controllers;

use App\BusinessService;
use App\Company;
use App\Deal;
use App\GlobalSetting;
use App\Helper\Formats;
use App\Location;
use App\ThemeSetting;
use App\TodoItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use App\Language;
use App\ModuleSetting;
use App\Package;
use App\PaymentGatewayCredentials;
use App\Role;
use App\SmsSetting;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Country;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Laravel\Cashier\Subscription;
use function PHPUnit\Framework\isNull;

class AdminBaseController extends Controller
{
    public $user;
    public $pageTitle;
    public $settings;
    public $lat_long;
    public $productsCount;
    public $adminCredentials;

    public function __construct()
    {

        parent::__construct();

        $this->smsSettings = SmsSetting::first();
        $this->languages = Language::where('status', 'enabled')->orderBy('language_name', 'asc')->get();
        $this->locations = Location::select('id', 'name')->get();
        $this->paymentCredential = PaymentGatewayCredentials::first();

        view()->share('smsSettings', $this->smsSettings);
        view()->share('languages', $this->languages);
        view()->share('locations', $this->locations);
        view()->share('paymentCredential', $this->paymentCredential);
        view()->share('calling_codes', $this->getCallingCodes());

        $this->middleware('auth')->only(['paymentGateway', 'offlinePayment', 'paymentConfirmation']);

        $this->middleware(function ($request, $next)
        {
            $this->themeSettings = ThemeSetting::first();
            $this->productsCount = request()->hasCookie('products') ? count(json_decode(request()->cookie('products'), true)) : 0;
            $this->user = auth()->user();

//            $this->superadmin = GlobalSetting::first();
            $this->superadmin = GlobalSetting::first();
            if ($this->user) {
                $this->todoItems = $this->user->todoItems()->groupBy('status', 'position')->get();
//                config(['froiden_envato.allow_users_id' => true]);

                if($this->user->hasRole('superadmin')){
                    $compId = Session::get('company_id');

                    if ($compId !== '' && $compId !== null) {
//                        $companyId = Company::where('id', $compId)->first();
                        $companyId = company();
                    }
                }

                /*$this->settings = Cache::remember('settings', 60*60*24, function (){
                    return company();
                });*/
                $this->settings = company();
            }

            if($this->user->hasRole('customer')){
//                $this->settings = Cache::remember('settings', 60*60*24, function (){
//                    return company();
//                });
                $this->settings = company();
            }

            $this->user->hasRole('customer') ? config(['app.name' => $this->user->name]) : config(['app.name' => $this->settings->company_name]);

            $this->settings = $this->user->hasRole('customer') ? $this->superadmin : $this->settings;

            // config(['app.name' => $this->settings->company_name]);
            config(['app.url' => url('/')]);
            App::setLocale($this->settings->locale);

            if(!is_null($this->settings->lat_long)){
                $this->lat_long = [
                    'latitude' => $this->settings->lat_long->getLat(),
                    'longitude' => $this->settings->lat_long->getLng()
                ];
            }else{
                 $this->lat_long = [
                     'latitude' => 38.752100015409326,
                     'longitude' => -9.200870017148958
                 ];
            }
            view()->share('lat_long', $this->lat_long);
            view()->share('superadmin', $this->superadmin);
            view()->share('user', $this->user);
            view()->share('settings', $this->settings);

            view()->share('themeSettings', $this->themeSettings);
            view()->share('productsCount', $this->productsCount);
            view()->share('date_picker_format', Formats::dateFormats()[$this->settings->date_format]);
            view()->share('date_format', Formats::datePickerFormats()[$this->settings->date_format]);
            view()->share('time_picker_format', Formats::timeFormats()[$this->settings->time_format]);
            $this->package = Package::find($this->settings->package_id);
            $this->total_employees = User::otherThanCustomers()->count();
            $this->total_deals = Deal::count();
            $this->total_business_services = BusinessService::count();
            $this->total_roles = Role::count();

            view()->share('package_setting', $this->package);
            view()->share('total_employees', $this->total_employees);
            view()->share('total_deals', $this->total_deals);
            view()->share('total_business_services', $this->total_business_services);
            view()->share('total_roles', $this->total_roles);

            return $next($request);
        });
    }

    public function checkMigrateStatus()
    {
        return checkMigrateStatus();
    }

    public function getCallingCodes()
    {

        $codes = [];
        $location = Location::where('country_id', '!=', null)->pluck('country_id');

        $countries = Country::all();

        foreach($countries as $country) {
            $codes = Arr::add($codes, $country->iso, array('name' => $country->name, 'dial_code' => '+'.$country->phonecode, 'code' => $country->iso));
        }

        return $codes;
    }

    public function generateTodoView()
    {
        $pendingTodos = $this->user->todoItems()->status('pending')->orderBy('position', 'DESC')->limit(5)->get();
        $completedTodos = $this->user->todoItems()->status('completed')->orderBy('position', 'DESC')->limit(5)->get();
        $dateFormat = $this->settings->date_format;

        $view = view('partials.todo_items_list', compact('pendingTodos', 'completedTodos', 'dateFormat'))->render();

        return $view;
    }

}
