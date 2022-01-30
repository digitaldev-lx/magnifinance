<?php

namespace App\Http\Controllers;

use App\Company;
use App\Language;
use App\ThemeSetting;
use App\GlobalSetting;
use App\UniversalSearch;
use App\FrontThemeSetting;
use App\SocialAuthSetting;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /**
     * @var array
     */
    public $data = [];

    /**
     * __set
     *
     * @param  string $name
     * @param  string $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public $user;
    public $pageTitle;
    public $settings;
    public $productsCount;
    public $superAdminThemeSetting;
    public $adminThemeSetting;
    public $customerThemeSetting;

    public function __construct()
    {
//        $this->showInstall();
//        $this->checkMigrateStatus();

        $this->settings = GlobalSetting::first();

        $this->frontThemeSettings = FrontThemeSetting::first();
        $this->popularSearch = UniversalSearch::withoutGlobalScope(CompanyScope::class)->where('type', 'frontend')->where('count', '>', 0)->orderBy('count', 'desc')->limit(7)->get();
        $this->popularStores = Company::where('popular_store', '1')->limit(7)->get();
        $this->languages = Language::where('status', 'enabled')->orderBy('language_name', 'asc')->get();
        $this->socialAuthSettings = SocialAuthSetting::first();

        if($this->settings){
            config(['app.name' => $this->settings->company_name]);
        }

        view()->share('languages', $this->languages);
        view()->share('settings', $this->settings);
        view()->share('popularSearch', $this->popularSearch);
        view()->share('popularStores', $this->popularStores);
        view()->share('frontThemeSettings', $this->frontThemeSettings);

        $this->middleware(function ($request, $next) {
            $this->superAdminThemeSetting = ThemeSetting::ofSuperAdminRole()->first();
            $this->adminThemeSetting = ThemeSetting::ofAdminRole()->first();
            $this->customerThemeSetting = ThemeSetting::ofCustomerRole()->first();

            $this->productsCount = request()->hasCookie('products') ? count(json_decode(request()->cookie('products'), true)) : 0;
            $this->user = auth()->user();

            if ($this->user) {
                $this->todoItems = $this->user->todoItems()->groupBy('status', 'position')->get();
                config(['froiden_envato.allow_users_id' => true]);
            }

            view()->share('user', $this->user);
            view()->share('productsCount', $this->productsCount);
            view()->share('superAdminThemeSetting', $this->superAdminThemeSetting);
            view()->share('adminThemeSetting', $this->adminThemeSetting);
            view()->share('customerThemeSetting', $this->customerThemeSetting);

//            App::setLocale($this->settings->locale);

            return $next($request);
        });
    }

    public function checkMigrateStatus()
    {
        return checkMigrateStatus();
    }

}
