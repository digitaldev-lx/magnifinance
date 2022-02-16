<?php

namespace App\Http\Controllers;

use App\Category;
use App\Country;
use App\FooterSetting;
use App\FrontThemeSetting;
use App\FrontWidget;
use App\GlobalSetting;
use App\GoogleCaptchaSetting;
use App\Helper\Formats;
use App\Language;
use App\Location;
use App\Page;
use App\Section;
use App\SmsSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class FrontBaseController extends Controller
{
    public $user;
    public $pageTitle;
    public $settings;

    public function __construct()
    {
        parent::__construct();

        $this->smsSettings = SmsSetting::first();
        $this->googleCaptchaSettings = GoogleCaptchaSetting::first();
        $this->settings = GlobalSetting::first();
        $this->frontThemeSettings = FrontThemeSetting::first();

        $this->locations = Location::select('id', 'name')->active()->get();
        $this->languages = Language::where('status', 'enabled')->orderBy('language_name', 'asc')->get();
        $this->pages = Page::all();
        $this->productsCount = json_decode(request()->cookie('products'), true);
        $this->sections = Section::active()->get()->toArray();
        $this->footerSetting = FooterSetting::first();
        $this->widgets = FrontWidget::all();
        $this->countries = Country::all();

        $this->headerCategories = Category::withoutGlobalScopes()->active()->has('services', '>', 0)
            ->whereHas('services', function ($query) {
                $query->active();
            })
        ->withCount('services')
        ->get();

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

            $this->localeLanguage = Language::where('language_code', App::getLocale())->first();

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
        $location = Location::where('country_id', '!=', null)->pluck('country_id');
        $countries = count($location) > 0 ? Country::whereIn('id', $location)->get() : Country::get();

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
