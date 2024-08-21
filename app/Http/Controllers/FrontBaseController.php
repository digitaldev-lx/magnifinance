<?php

namespace App\Http\Controllers;

use App\Helper\Formats;
use App\Models\Category;
use App\Models\Country;
use App\Models\FooterSetting;
use App\Models\FrontThemeSetting;
use App\Models\FrontWidget;
use App\Models\GlobalSetting;
use App\Models\GoogleCaptchaSetting;
use App\Models\Language;
use App\Models\Location;
use App\Models\Page;
use App\Models\Section;
use App\Models\SmsSetting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class FrontBaseController extends Controller
{
    public $user;
    public $pageTitle;
    public $settings;

    public function __construct()
    {
        parent::__construct();

        $this->smsSettings = cache()->remember('SmsSetting', 60*60, function () {
            return SmsSetting::first();
        });
        $this->googleCaptchaSettings = cache()->remember('GoogleCaptchaSetting', 60*60, function () {
            return GoogleCaptchaSetting::first();
        });
        $this->settings = cache()->remember('GlobalSetting', 60*60, function () {
            return GlobalSetting::first();
        });
        $this->frontThemeSettings = cache()->remember('FrontThemeSetting', 60*60, function () {
            return FrontThemeSetting::first();
        });

        $this->locations = cache()->remember('Locations', 60*60, function () {
            return Location::select('id', 'name')->active()->get();
        });
        $this->languages = cache()->remember('language_enabled', 60*60, function () {
            return Language::where('status', 'enabled')->orderBy('language_name', 'asc')->get();
        });
        $this->pages = cache()->remember('Pages', 60*60, function () {
            return Page::all();
        });
        $this->productsCount = json_decode(request()->cookie('products'), true);
        $this->sections = cache()->remember('Sections', 60*60, function () {
            return Section::active()->get()->toArray();
        });
        $this->footerSetting = cache()->remember('FooterSetting', 60*60, function () {
            return FooterSetting::first();
        });
        $this->widgets = cache()->remember('FrontWidget', 60*60, function () {
            return FrontWidget::all();
        });
        $this->countries = cache()->remember('Countries', 60*60, function () {
            return Country::all();
        });

        $this->headerCategories = cache()->remember('Category_services', 60*60, function () {
            return Category::withoutGlobalScopes()->active()->has('services', '>', 0)
                ->whereHas('services', function ($query) {
                    $query->active();
                })
                ->withCount('services')
                ->get();
        });

        view()->share('widgets', $this->widgets);
        view()->share('productsCount', $this->productsCount);
        view()->share('smsSettings', $this->smsSettings);
        view()->share('googleCaptchaSettings', $this->googleCaptchaSettings);
        view()->share('settings', $this->settings);
        view()->share('frontThemeSettings', $this->frontThemeSettings);
        view()->share('locations', $this->locations);
        view()->share('languages', $this->languages);
        view()->share('pages', $this->pages);
        view()->share('calling_codes', $this->getCallingCodes());
        view()->share('footer_setting', $this->footerSetting);
        view()->share('headerCategories', $this->headerCategories);
        view()->share('countries', $this->countries);

        $this->middleware(function ($request, $next) {
            $this->productsCount = request()->hasCookie('products') ? count(json_decode(request()->cookie('products'), true)) : 0;
            $this->user = auth()->user();

            if ($this->user) {
                $this->todoItems = $this->user->todoItems()->groupBy('status', 'position')->get();
            }

            config(['app.name' => $this->settings->company_name]);
            config(['app.url' => url('/')]);

            App::setLocale($this->settings->locale);

            $this->localeLanguage = cache()->remember('language_code', 60*60, function () {
                return Language::where('language_code', App::getLocale())->first();
            });

            view()->share('sections', $this->sections);
            view()->share('user', $this->user);
            view()->share('productsCount', $this->productsCount);
            view()->share('date_picker_format', Formats::dateFormats()[$this->settings->date_format]);
            view()->share('date_format', Formats::datePickerFormats()[$this->settings->date_format]);
            view()->share('time_picker_format', Formats::timeFormats()[$this->settings->time_format]);


            /*if (request()->hasCookie('localstorage_language_code

')) {
                App::setLocale(Cookie::get('localstorage_language_code

'));
            }*/

            return $next($request);
        });
    }

    public function getCallingCodes()
    {
        $codes = [];
        $location = cache()->remember('country_id_location', 60*60, function () {
            return Location::where('country_id', '!=', null)->pluck('country_id');
        });
        $countries = cache()->remember('Country', 60*60, function () use ($location) {
            return count($location) > 0 ? Country::whereIn('id', $location)->get() : Country::get();
        });

        foreach($countries as $country) {
            $codes = Arr::add($codes, $country->iso, array('name' => $country->name, 'dial_code' => '+'.$country->phonecode));
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
