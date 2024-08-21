<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\FrontThemeSetting;
use App\Models\GlobalSetting;
use App\Models\Language;
use App\Models\SocialAuthSetting;
use App\Models\ThemeSetting;
use App\Models\UniversalSearch;
use App\Scopes\CompanyScope;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

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

        $this->settings = cache()->remember('GlobalSetting', 60*60, function () {
            return GlobalSetting::first();
        });;

        $this->frontThemeSettings = cache()->remember('FrontThemeSetting', 60*60, function () {
            return FrontThemeSetting::first();
        });
        $this->popularSearch = cache()->remember('UniversalSearch', 60*60, function () {
            return UniversalSearch::withoutGlobalScope(CompanyScope::class)->where('type', 'frontend')->where('count', '>', 0)->orderBy('count', 'desc')->limit(7)->get();
        });
        $this->popularStores = cache()->remember('popular_store', 60*60, function () {
            return Company::where('popular_store', '1')->limit(7)->get();
        });
        $this->languages = cache()->remember('language_enabled', 60*60, function () {
            return Language::where('status', 'enabled')->orderBy('language_name', 'asc')->get();
        });
        $this->socialAuthSettings = cache()->remember('SocialAuthSetting', 60*60, function () {
            return SocialAuthSetting::first();
        });

        if($this->settings){
            config(['app.name' => $this->settings->company_name]);
        }

        view()->share('languages', $this->languages);
        view()->share('settings', $this->settings);
        view()->share('popularSearch', $this->popularSearch);
        view()->share('popularStores', $this->popularStores);
        view()->share('frontThemeSettings', $this->frontThemeSettings);

        $this->middleware(function ($request, $next) {
            $this->superAdminThemeSetting = cache()->remember('ThemeSetting_ofSuperAdminRole', 60*60, function () {
                return ThemeSetting::ofSuperAdminRole()->first();
            });
            $this->adminThemeSetting = cache()->remember('ThemeSetting_ofAdminRole', 60*60, function () {
                return ThemeSetting::ofAdminRole()->first();
            });
            $this->customerThemeSetting = cache()->remember('ThemeSetting_ofCustomerRole', 60*60, function () {
                return ThemeSetting::ofCustomerRole()->first();
            });

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
