<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Formats;
use App\Helper\Permissions;
use App\Helper\Reply;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\Setting\UpdateNote;
use App\Http\Requests\Setting\UpdateSetting;
use App\Http\Requests\Setting\UpdateTerms;
use App\Models\Article;
use App\Models\BookingTime;
use App\Models\BusinessService;
use App\Models\Company;
use App\Models\Country;
use App\Models\Currency;
use App\Models\CurrencyFormatSetting;
use App\Models\GlobalSetting;
use App\Models\Language;
use App\Models\Media;
use App\Models\Module;
use App\Models\ModuleSetting;
use App\Models\Package;
use App\Models\PackageModules;
use App\Models\PaymentGatewayCredentials;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SmsSetting;
use App\Models\SmtpSetting;
use App\Models\SocialAuthSetting;
use App\Services\ImagesManager;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SettingController extends SuperAdminBaseController
{

    private $image;

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.settings'));
        $this->image = new ImagesManager();
    }

    public function index()
    {
        abort_if(!$this->user->is_superadmin_employee || !$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('manage_settings'), 403);

        $this->bookingTimes = BookingTime::all();
        $this->images = Media::select('id', 'image')->latest()->get();
        $this->timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $this->dateFormats = Formats::dateFormats();
        $this->timeFormats = Formats::timeFormats();
        $this->dateObject = Carbon::now($this->settings->timezone);
        $this->currencies = Currency::all();
        $this->currencyFormatSetting = CurrencyFormatSetting::first();
        $this->enabledLanguages = Language::where('status', 'enabled')->orderBy('language_name')->get();
        $this->smtpSetting = SmtpSetting::first();
        $this->credentialSetting = PaymentGatewayCredentials::first();
        $this->smsSetting = SmsSetting::first();

        $this->roles = Role::whereNotIn('name', ['superadmin', 'administrator', 'employee', 'customer'])->whereNull('company_id')->get();
        $this->totalPermissions = Permission::count();
        $this->modules = Module::whereIn('name', Permissions::getModules($this->user->role))->get();
        $this->moduleSettings = ModuleSetting::where('status', 'deactive')->get();
        $this->socialCredentials = SocialAuthSetting::first();
        $this->countries = Country::all();

        $this->package_modules = PackageModules::get();
        $this->package = Package::trialPackage()->first();

        $arr = json_decode($this->package->package_modules, true);
        $selected_package_modules = [];

        if(!is_null($arr)) {

            foreach($arr as $value) {
                $selected_package_modules[] = $value;
            }
        }

        $this->selected_package_modules = $selected_package_modules;

        return view('superadmin.settings.index', $this->data);
    }

    public function editNote()
    {

        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('manage_settings'), 403);

        $this->setting = GlobalSetting::first();
        return view('superadmin.front-faq-settings.edit_note', $this->data);
    }

    // @codingStandardsIgnoreLine
    public function updateNote(UpdateNote $request, $id)
    {
        $setting = GlobalSetting::first();
        $setting->sign_up_note = $request->sign_up_note;
        $setting->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    public function sitemapDownload()
    {
        $path = "sitemap/sitemap.xml";
        return Storage::disk('local')->download($path, 'sitemap.xml', ['Content-Type' => 'application/xml']);
    }

    public function sitemapGenerate()
    {
        $path = "sitemap/sitemap.xml";
        $sitemap = Sitemap::create(env('APP_URL'))
            ->add(Url::create('/terms-and-conditions')->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY))
            ->add(Url::create('/privacy-policy')->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY))
            ->add(Url::create('/contact-us')->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY))
            ->add(Url::create('/about-us')->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY))
            ->add(Url::create('/how-it-works')->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY));

        Company::all()->each(function (Company $company) use ($sitemap) {
            $sitemap->add(Url::create("/vendor/$company->slug")
                ->setLastModificationDate($company->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));
        });

        Article::whereStatus('approved')->each(function (Article $article) use ($sitemap) {
            $sitemap->add(Url::create("/blog/$article->slug")
                ->setLastModificationDate($article->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));
        });

        BusinessService::whereStatus('active')->each(function (BusinessService $service) use ($sitemap) {
            $sitemap->add(Url::create("/service/$service->company_id/$service->slug")
                ->setLastModificationDate($service->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
        });

        $sitemapFile = $sitemap->writeToDisk('r2', $path);

        if($sitemapFile) {
            return response()->json(["status" => true]);
        }else{
            return response()->json(["status" => false]);
        }
    }

    public function editTerms()
    {

        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('manage_settings'), 403);

        $this->setting = GlobalSetting::first();
        return view('superadmin.front-faq-settings.edit_terms', $this->data);
    }

    // @codingStandardsIgnoreLine
    public function updateTerms(UpdateTerms $request, $id)
    {
        $setting = GlobalSetting::first();
        $setting->terms_note = $request->terms_note;
        $setting->save();

        return Reply::success(__('messages.updatedSuccessfully'));

    }

    // @codingStandardsIgnoreLine
    public function update(UpdateSetting $request, $id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('manage_settings'), 403);

        $companyId = User::select('company_id')->where('id', Auth::user()->id)->first()->company_id;

        $setting = GlobalSetting::first();
        $setting->company_name = $request->company_name;
        $setting->vat_number = $request->vat_number;
        $setting->company_email = $request->company_email;
        $setting->company_phone = $request->company_phone;
        $setting->address = $request->address;
        $setting->post_code = $request->post_code;
        $setting->city = $request->city;
        $setting->country_id = $request->country_id;
        $setting->date_format = $request->date_format;
        $setting->time_format = $request->time_format;
        $setting->website = $request->website;
        $setting->timezone = $request->timezone;
        $setting->locale = $request->input('locale');
        $setting->currency_id = $request->currency_id;

        if ($request->hasFile('logo')) {
            $this->image->deleteImage($setting->logo);
            $setting->logo = $this->image->storeImage($request, 'logo', 'logo');
        }

        $setting->save();

        if ($setting->currency->currency_code !== 'INR') {
            $credential = PaymentGatewayCredentials::first();

            if ($credential->razorpay_status == 'active') {
                $credential->razorpay_status = 'deactive';

                $credential->save();
            }
        }

        cache()->forget('global_setting');

        // Update package curreny_id
        $this->updatePackageCurrencies($setting->currency_id);

        return Reply::redirect(route('superadmin.settings.index'), __('messages.updatedSuccessfully'));
    }

    protected function updatePackageCurrencies($currency_id)
    {
        DB::table('packages')->update(array('currency_id' => $currency_id));
    }

    public function changeLanguage($code)
    {
        $language = Language::where('language_code', $code)->first();

        if ($language) {
            $this->settings->locale = $code;
        }
        else if ($code == 'en') {
            $this->settings->locale = 'en';
        }

        $this->settings->save();
        Cache::forget('global');
        App::setLocale($this->settings->locale);
        return Reply::success(__('messages.languageChangedSuccessfully'));
    }

    public function freeTrialSetting(Request $request)
    {
        $package = Package::find($request->id);
        $package->name = $request->name;
        $package->max_employees = $request->max_employees;
        $package->max_services = $request->max_services;
        $package->max_deals = $request->max_deals;
        $package->max_roles = $request->max_roles;
        $package->no_of_days = $request->no_of_days;
        $package->notify_before_days = $request->notify_before_days;
        $package->trial_message = $request->trial_message;
        $package->description = $request->description;
        $package->status = is_null($request->status) ? 'inactive' : $request->status;
        $package->package_modules = json_encode($request->package_modules);
        $package->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    public function editContactDetails(Request $request)
    {
        $globalSetting = GlobalSetting::first();
        $globalSetting->contact_email = $request->contact_email;
        $globalSetting->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    public function editMapKey(Request $request)
    {
        $globalSetting = GlobalSetting::first();

        if ($request->map_option) {
            $globalSetting->map_option = $request->map_option;
        }
        else {
            $globalSetting->map_option = 'deactive';
        }

        $globalSetting->map_key = $request->map_key;
        $globalSetting->save();
        cache()->forget('global_setting');

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    public function saveGoogleCalendarConfig(Request $request)
    {
        $globalSetting = GlobalSetting::first();

        if ($request->google_calendar) {
            $globalSetting->google_calendar = $request->google_calendar;
        }
        else {
            $globalSetting->google_calendar = 'deactive';
        }

        $globalSetting->google_client_id = $request->google_client_id;
        $globalSetting->google_client_secret = $request->google_client_secret;
        $globalSetting->save();
        cache()->forget('global_setting');

        return Reply::success(__('messages.updatedSuccessfully'));
    }

}
