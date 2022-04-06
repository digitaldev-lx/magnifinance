<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\Services\ImagesManager;
use App\Services\UrlManager;
use App\Tax;
use App\Role;
use App\User;
use App\Media;
use App\Module;
use App\Company;
use App\Currency;
use App\Language;
use Carbon\Carbon;
use App\Permission;
use App\SmsSetting;
use App\VendorPage;
use App\BookingTime;
use App\OfficeLeave;
use App\SmtpSetting;
use App\Helper\Files;
use App\Helper\Reply;
use App\ModuleSetting;
use GuzzleHttp\Client;
use App\Helper\Formats;
use App\Helper\Permissions;
use Illuminate\Http\Request;
use App\GatewayAccountDetail;
use App\PaymentGatewayCredentials;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Http\Requests\Setting\UpdateSetting;
use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Admin\Company\BookingSetting;

class SettingController extends AdminBaseController
{

    private $image;
    public function __construct()
    {
        parent::__construct();
        $this->image = new ImagesManager();
        view()->share('pageTitle', __('menu.settings'));
    }

    public function index()
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('manage_settings'), 403);

        $this->bookingTimes = BookingTime::all();
        $this->images = Media::select('id', 'image')->latest()->get();
        $this->tax = Tax::active()->first();
        $this->timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $this->dateFormats = Formats::dateFormats();
        $this->timeFormats = Formats::timeFormats();
        $this->dateObject = Carbon::now($this->settings->timezone);
        $this->currencies = Currency::all();
        $this->countries = Country::all();
        $this->enabledLanguages = Language::where('status', 'enabled')->orderBy('language_name')->get();
        $this->smtpSetting = SmtpSetting::first();
        $this->credentialSetting = PaymentGatewayCredentials::first();
        $this->smsSetting = SmsSetting::first();
        $this->roles = Role::whereNotIn('name', ['superadmin', 'administrator', 'agent'])->where('company_id', $this->user->company_id)->get();
        $this->totalPermissions = Permission::count();
        $this->modules = Module::whereIn('name', Permissions::getModules($this->user->role))->get();
        $this->moduleSettings = ModuleSetting::where('status', '=','deactive')->get();
        $employees = User::AllEmployees()->get();

        /*$client = new Client();
        $res = $client->request('GET', config('froiden_envato.updater_file_path'), ['verify' => false]);
        $this->lastVersion = $res->getBody();
        $this->lastVersion = json_decode($this->lastVersion, true);
        $currentVersion = File::get('version.txt');

        $description = $this->lastVersion['description'];

        $this->newUpdate = 0;

        if (version_compare($this->lastVersion['version'], $currentVersion) > 0) {
            $this->newUpdate = 1;
        }

        $this->updateInfo = $description;
        $this->lastVersion = $this->lastVersion['version'];

        $this->appVersion = File::get('version.txt');
        $laravel = app();
        $this->laravelVersion = $laravel::VERSION;*/

        $this->officeLeaves = OfficeLeave::all();

        $this->stripePaymentSetting = GatewayAccountDetail::ofStatus('active')->ofGateway('stripe')->first();
//        dd($this->stripePaymentSetting->stripe_login_link->url);
        $this->razoypayPaymentSetting = GatewayAccountDetail::ofStatus('active')->ofGateway('razorpay')->first();

        $this->vendorPage = VendorPage::first();
        $this->companyBookingNotification = company()->bookingNotification;
        return view('admin.settings.index', $this->data);
    }

    // @codingStandardsIgnoreLine
    public function update(UpdateSetting $request, $id)
    {
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('manage_settings'), 403);
        try {
            DB::beginTransaction();
            $company = User::with('company')->where('id', auth()->user()->id)->first();

            $setting = Company::findOrFail($company->company->id);
            $setting->company_name = $request->company_name;
            $setting->company_email = $request->company_email;
            $setting->company_phone = $request->company_phone;
            $setting->address = $request->address;
            $setting->post_code = $request->post_code;
            $setting->city = $request->city;
            $setting->country_id = $request->country_id;
            $setting->vat_number = $request->vat_number;
            $setting->date_format = $request->date_format;
            $setting->time_format = $request->time_format;
            $setting->website = $request->website;
            $setting->timezone = $request->timezone;
            $setting->locale = $request->input('locale');
            $setting->currency_id = $request->currency_id;

            if ($request->hasFile('logo')) {
                $this->image->deleteImage($setting->logo);
                $setting->logo = $this->image->storeImage($request, 'company-logo','logo');
            }

            $setting->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort_and_log(403, $e->getMessage());
        }

        return Reply::redirect(route('admin.settings.index'), __('messages.updatedSuccessfully'));
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
        Cache::forget('settings');
        App::setLocale($this->settings->locale);

        return Reply::success(__('messages.languageChangedSuccessfully'));
    }

    public function saveBookingTimesField(BookingSetting $request)
    {
        $company = User::with('company')->where('id', auth()->user()->id)->first();

        $setting = Company::findOrFail($company->company->id);
        $setting->booking_per_day = $request->no_of_booking_per_customer;
        $setting->multi_task_user = $request->multi_task_user;
        $setting->employee_selection = $request->employee_selection;
        $setting->disable_slot = $request->disable_slot ?? 'disabled';
        $setting->booking_time_type = $request->booking_time_type ;
        $setting->cron_status = $request->cron_status;

        if (!$request->cron_status) {
            $setting->cron_status = 'deactive';
        }

        $setting->duration = $request->duration;
        $setting->duration_type = $request->duration_type;
        $setting->save();

        if ($request->disable_slot == 'enabled') {
            DB::table('payment_gateway_credentials')->where('id', 1)->update(['show_payment_options' => 'hide', 'offline_payment' => 1]);
        }

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    public function moduleSetting()
    {
        $package_modules = json_decode($this->package->package_modules, true) ?: [];

        $admin_modules = ModuleSetting::select('module_name')->where(['type' => 'administrator', 'status' => 'active'])->get()->map(function ($item, $key) {return $item->module_name;
        })->toArray();

        $employee_modules = ModuleSetting::select('module_name')->where(['type' => 'employee', 'status' => 'active'])->get()->map(function ($item, $key) {return $item->module_name;
        })->toArray();

        return view('admin.settings.module-settings', compact('package_modules', 'admin_modules', 'employee_modules'));
    }

    public function updateModuleSetting(Request $request)
    {
        $company = User::with('company')->where('id', auth()->user()->id)->first();

        ModuleSetting::where(['company_id' => $company->company->id, 'module_name' => $request->module_name, 'type' => $request->user_type])
            ->update(['status' => $request->status]);

        return Reply::success(__('messages.updatedSuccessfully'));
    }

} /* end of class */
