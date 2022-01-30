<?php

namespace App\Http\Controllers\SuperAdmin;

use App\BookingTime;
use App\Currency;
use App\FooterSetting;
use App\FrontFaq;
use App\FrontWidget;
use App\Helper\Formats;
use App\Helper\Reply;
use App\Language;
use App\Media;
use App\PaymentGatewayCredentials;
use App\SmtpSetting;
use GuzzleHttp\Client;
use App\Http\Controllers\SuperAdminBaseController;
use App\Http\Requests\FrontSetting\UpdateFrontSettings;
use App\Section;
use App\SmsSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class FrontSettingController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.settings'));

    }

    public function index()
    {

        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('manage_settings'), 403);

        $this->bookingTimes = BookingTime::all();
        $this->images = Media::select('id', 'image')->latest()->get();
        $this->timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $this->dateFormats = Formats::dateFormats();
        $this->timeFormats = Formats::timeFormats();
        $this->dateObject = Carbon::now($this->settings->timezone);
        $this->currencies = Currency::all();
        $this->enabledLanguages = Language::where('status', 'enabled')->orderBy('language_name')->get();
        $this->smtpSetting = SmtpSetting::first();
        $this->credentialSetting = PaymentGatewayCredentials::first();
        $this->smsSetting = SmsSetting::first();
        $this->footerSetting = FooterSetting::first();
        $this->frontFaqs = FrontFaq::select('languages.language_name', 'front_faqs.question', 'front_faqs.answer', 'front_faqs.id as faq_id')->join('languages', 'languages.id', 'front_faqs.language_id')->get();
        $this->frontWidgets = FrontWidget::all();
        $this->sections = Section::get();

        $client = new Client();
        $res = $client->request('GET', config('froiden_envato.updater_file_path'), ['verify' => false]);
        $lastVersion = $res->getBody();
        $lastVersion = json_decode($lastVersion, true);
        $currentVersion = File::get('version.txt');

        $description = $lastVersion['description'];

        $newUpdate = 0;

        if (version_compare($lastVersion['version'], $currentVersion) > 0)
        {
            $newUpdate = 1;
        }

        $this->updateInfo = $description;
        $this->lastVersion = $lastVersion['version'];

        $this->appVersion = File::get('version.txt');
        $laravel = app();
        $this->laravelVersion = $laravel::VERSION;

        return view('superadmin.front-settings.index', $this->data);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function update(UpdateFrontSettings $request, $id)
    {
        $setting = FooterSetting::findOrFail($id);

        $links = [];

        foreach ($request->social_links as $name => $value) {
            $link_details = [];
            $link_details = Arr::add($link_details, 'name', $name);
            $link_details = Arr::add($link_details, 'link', $value);
            array_push($links, $link_details);
        }

        $setting->social_links = $links;
        $setting->footer_text = $request->footer_text;

        $setting->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    public function changeSectionStatus(Request $request)
    {
        $section = Section::find($request->id);
        $section->status = $request->status;
        $section->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

}
