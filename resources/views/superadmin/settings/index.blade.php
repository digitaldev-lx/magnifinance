@extends('layouts.master')

@push('head-css')
    <link rel="stylesheet" href="{{ asset('css/bootstrap-tagsinput.css') }}">
    <link href="{{asset('assets/plugins/swal/sweetalert.css')}}" rel="stylesheet">
    <style>
        .dropify-wrapper,
        .dropify-preview,
        .dropify-render img {
            background-color: var(--sidebar-bg) !important;
        }

        #carousel-image-gallery .card .img-holder {
            height: 150px;
            overflow: hidden;
        }

        #carousel-image-gallery .card .img-holder img {
            height: 100%;
            object-fit: cover;
            object-position: top;
        }

        .note-group-select-from-files {
            display: none;
        }

        .select2-container .select2-selection--single {
            height: 39px;
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #d2d1d1;
            border-radius: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #444;
            line-height: 37px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px;
            position: absolute;
            top: 5px;
            right: 1px;
            width: 20px;
        }

        .bootstrap-tagsinput {
            width: 100%;
        }

        .bootstrap-tagsinput .tag {
            margin-right: 2px;
            padding: 2px 5px;
            border-radius: 2px;
        }

        .d-none {
            display: none;
        }
    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-12 col-md-2 mb-4 mt-3 mb-md-0 mt-md-0">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <a class="nav-link active" href="#general" data-toggle="tab" id="general-tab">@lang('menu.general')</a>
                <a class="nav-link" href="#currency" data-toggle="tab">@lang('app.currency') @lang('menu.settings')</a>
                <a class="nav-link" href="#language" data-toggle="tab">@lang('app.language') @lang('menu.settings')</a>
                <a class="nav-link" href="#email" data-toggle="tab">@lang('app.email') @lang('menu.settings')</a>
                <a class="nav-link" href="#theme" data-toggle="tab">@lang('menu.themeSettings')</a>
                <a class="nav-link" href="#tax" data-toggle="tab">@lang('app.tax') @lang('menu.settings')</a>
                <a class="nav-link" href="#seo-settings" data-toggle="tab">@lang('menu.seoSettings')</a>
                <a class="nav-link" href="#contact-settings" data-toggle="tab">@lang('menu.contactSettings')</a>
                <a class="nav-link" href="#map-configuration"
                   data-toggle="tab">@lang('menu.mapConfiguration') @lang('menu.settings')</a>
                <a class="nav-link" href="#googleCalendar" data-toggle="tab">@lang('menu.googleCalendar')</a>
                <a class="nav-link payment-setting-page" href="#">@lang('menu.paymentSettings')</a>
                <a class="nav-link" href="#social-login-settings" data-toggle="tab">@lang('app.socialLogin')
                    @lang('menu.settings')</a>
                <a class="nav-link" href="#sms-settings" data-toggle="tab">@lang('app.smsCredentials')
                    @lang('menu.settings')</a>
                <a class="nav-link" href="#role-permission" data-toggle="tab">@lang('menu.rolesPermissions')</a>
                <a class="nav-link" href="#ticket-settings" data-toggle="tab">@lang('menu.ticketSettings')</a>
                <a class="nav-link" href="#free-trial" data-toggle="tab">@lang('menu.freeTrialSettings')</a>
                <a class="nav-link front-setting-page" href="#">@lang('menu.frontSettings')</a>
                <a class="nav-link update-app" href="#">@lang('menu.updateApp')
                    @if($newUpdate == 1)
                        <span class="badge badge-success">{{ $lastVersion }}</span>
                    @endif
                </a>
            </div>
        </div>
        <div class="col-12 col-md-10">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content">
                                {{-- GENERAL TAB --}}
                                <div class="active tab-pane" id="general">
                                    <form class="form-horizontal ajax-form" id="general-form" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="tax_name" class="control-label">@lang('app.company')
                                                        @lang('app.name')</label>
                                                    <input type="text" class="form-control  form-control-lg"
                                                           id="company_name" name="company_name"
                                                           value="{{ $settings->company_name }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="tax_name" class="control-label">@lang('app.company')
                                                        @lang('app.email')</label>
                                                    <input type="text" class="form-control  form-control-lg"
                                                           id="company_email" name="company_email"
                                                           value="{{ $settings->company_email }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="tax_name" class="control-label">@lang('app.company')
                                                        @lang('app.phone')</label>
                                                    <input type="text" class="form-control  form-control-lg"
                                                           id="company_phone" name="company_phone"
                                                           value="{{ $settings->company_phone }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">@lang('app.logo')</label>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <input type="file" id="input-file-now" name="image"
                                                                   accept=".png,.jpg,.jpeg" class="dropify"
                                                                   data-default-file="{{ $settings->logo_url }}"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">@lang('app.address')</label>
                                                    <textarea class="form-control form-control-lg" name="address" id=""
                                                              cols="30" rows="5">{!! $settings->address !!}</textarea>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="date_format" class="control-label">
                                                                @lang('app.date_format')
                                                            </label>
                                                            <select name="date_format" id="date_format"
                                                                    class="form-control form-control-lg select2">
                                                                @foreach($dateFormats as $key => $dateFormat)
                                                                    <option value="{{ $key }}" @if($settings->date_format ==
                                                $key) selected @endif>{{
                                                $key.' ('.$dateObject->format($key).')' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="time_format" class="control-label">
                                                                @lang('app.time_format')
                                                            </label>
                                                            <select name="time_format" id="time_format"
                                                                    class="form-control form-control-lg select2">
                                                                @foreach($timeFormats as $key => $timeFormat)
                                                                    <option value="{{ $key }}" @if($settings->time_format ==
                                                $key) selected @endif>{{
                                                $key.' ('.$dateObject->format($key).')' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="tax_name" class="control-label">@lang('app.company')
                                                        @lang('app.website')</label>
                                                    <input type="text" class="form-control form-control-lg" id="website"
                                                           name="website" value="{{ $settings->website }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="tax_name"
                                                           class="control-label">@lang('app.timezone')</label>
                                                    <select name="timezone" id="timezone"
                                                            class="form-control form-control-lg select2">
                                                        @foreach($timezones as $tz)
                                                            <option @if($settings->timezone == $tz) selected @endif>{{ $tz }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="tax_name"
                                                           class="control-label">@lang('app.currency')</label>
                                                    <select name="currency_id" id="currency_id"
                                                            class="form-control  form-control-lg">
                                                        @foreach($currencies as $currency)
                                                            <option
                                                                @if($currency->id == $settings->currency_id) selected
                                                                @endif
                                                                value="{{ $currency->id }}">{{ $currency->currency_symbol.' ('.$currency->currency_code.')' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="tax_name"
                                                           class="control-label">@lang('app.language')</label>
                                                    <select name="locale" id="locale"
                                                            class="form-control form-control-lg">
                                                        @forelse($enabledLanguages as $language)
                                                            <option value="{{ $language->language_code }}" @if($settings->locale
                                            == $language->language_code) selected @endif >
                                                                {{ $language->language_name }}
                                                            </option>
                                                        @empty
                                                            <option @if($settings->locale == "en") selected
                                                                    @endif value="en">English
                                                            </option>
                                                        @endforelse
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <button id="save-general" type="button" class="btn btn-success"><i
                                                            class="fa fa-check"></i> @lang('app.save')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                {{-- GENERAL TAB --}}
                                {{-- CURRENCY TAB --}}
                                <div class="tab-pane" id="currency">
                                    <div class="tab-content" id="currencyTabContent">
                                        <ul class="nav nav-tabs mb-5" id="currencySettingsTab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="currencySettings-tab" data-toggle="tab"
                                                   href="#currencySettings"
                                                   role="tab" aria-controls="currencySettings"
                                                   aria-selected="true">@lang('app.currency')
                                                    @lang('menu.settings')</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="currencyFormateSettings-tab" data-toggle="tab"
                                                   href="#currencyFormateSettings"
                                                   role="tab" aria-controls="currencyFormateSettings"
                                                   aria-selected="true">@lang('app.currency')
                                                    @lang('app.format') @lang('menu.settings')</a>
                                            </li>
                                        </ul>
                                        <div class="tab-pane fade show active" id="currencySettings" role="tabpanel"
                                             aria-labelledby="currencySettings-tab">
                                            <h4>@lang('app.add') @lang('app.currency')</h4>
                                            <form class="form-horizontal ajax-form" id="currency-form" method="POST">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">@lang('app.currency')
                                                                @lang('app.name')</label>
                                                            <input type="text" class="form-control form-control-lg"
                                                                   id="currency_name"
                                                                   name="currency_name">
                                                        </div>
                                                        <div class="form-group">
                                                            <label
                                                                class="control-label">@lang('app.currencySymbol')</label>
                                                            <input type="text" class="form-control form-control-lg"
                                                                   id="currency_symbol"
                                                                   name="currency_symbol">
                                                        </div>
                                                        <div class="form-group">
                                                            <label
                                                                class="control-label">@lang('app.currencyCode')</label>
                                                            <input type="text" class="form-control form-control-lg"
                                                                   id="currency_code"
                                                                   name="currency_code">
                                                        </div>
                                                        <div class="form-group">
                                                            <label
                                                                class="control-label">@lang('app.exchangeRate')</label>
                                                            <input type="text" class="form-control form-control-lg"
                                                                   id="exchange_rate"
                                                                   name="exchange_rate">
                                                        </div>
                                                        <div class="form-group">
                                                            <button id="save-currency" type="button"
                                                                    class="btn btn-success"><i class="fa fa-check"></i>
                                                                @lang('app.save')</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <h4 class="mt-4">@lang('app.currency')</h4>
                                            <div class="row">
                                                <div class="col-md-12 table-responsive">
                                                    <table class="table table-condensed">
                                                        <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>@lang('app.currency') @lang('app.name')</th>
                                                            <th>@lang('app.currencySymbol')</th>
                                                            <th>@lang('app.currencyCode')</th>
                                                            <th>@lang('app.exchangeRate')</th>
                                                            <th class="text-right"><i class="fa fa-gear"></i></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach ($currencies as $key => $currency)
                                                            <tr id="currency-{{ $currency->id }}">
                                                                <td>{{ $key + 1 }}</td>
                                                                <td>{{ ucwords($currency->currency_name) }}</td>
                                                                <td>{{ $currency->currency_symbol }}</td>
                                                                <td>{{ $currency->currency_code }}</td>
                                                                <td>{{ $currency->exchange_rate }}</td>
                                                                <td class="text-right">
                                                                    <button data-row-id="{{ $currency->id }}"
                                                                            class="btn btn-primary btn-circle edit-currency"
                                                                            type="button"><i
                                                                            class="fa fa-pencil" data-toggle="tooltip"
                                                                            data-original-title="@lang('app.edit')"></i>
                                                                    </button>

                                                                    @if ($currency->currency_code !== 'USD' && $settings->currency->id !== $currency->id && !$currency->has_companies)
                                                                        <button data-row-id="{{ $currency->id }}"
                                                                                class="btn btn-danger btn-circle delete-currency"
                                                                                type="button"><i
                                                                                class="fa fa-times"
                                                                                data-toggle="tooltip"
                                                                                data-original-title="@lang('app.delete')"></i>
                                                                        </button>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="currencyFormateSettings" role="tabpanel"
                                             aria-labelledby="currencyFormateSettings-tab">
                                            <form class="form-horizontal ajax-form" id="currencyFormateForm"
                                                  method="POST">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label">@lang('app.currency')
                                                                        @lang('app.position')</label>
                                                                    <select name="currency_position"
                                                                            id="currency_position"
                                                                            class="form-control  form-control-lg">
                                                                        <option
                                                                            {{ $currencyFormatSetting->currency_position == 'left' ? 'selected' : '' }}
                                                                            value="left">@lang('app.left') </option>
                                                                        <option
                                                                            {{ $currencyFormatSetting->currency_position == 'right' ? 'selected' : '' }}
                                                                            value="right">@lang('app.right') </option>
                                                                        <option
                                                                            {{ $currencyFormatSetting->currency_position == 'left_with_space' ? 'selected' : '' }}
                                                                            value="left_with_space">@lang('app.leftWithSpace') </option>
                                                                        <option
                                                                            {{ $currencyFormatSetting->currency_position == 'right_with_space' ? 'selected' : '' }}
                                                                            value="right_with_space">@lang('app.rightWithSpace') </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label
                                                                        class="control-label">@lang('app.thousandSeparator')</label>
                                                                    <input type="text"
                                                                           class="form-control form-control-lg"
                                                                           id="thousand_separator"
                                                                           name="thousand_separator"
                                                                           value="{{ $currencyFormatSetting->thousand_separator }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label
                                                                        class="control-label">@lang('app.decimalSeparator')</label>
                                                                    <input type="text"
                                                                           class="form-control form-control-lg"
                                                                           id="decimal_separator"
                                                                           name="decimal_separator"
                                                                           value="{{ $currencyFormatSetting->decimal_separator }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label
                                                                        class="control-label">@lang('app.noOfDecimal')</label>
                                                                    <input type="text"
                                                                           class="form-control form-control-lg"
                                                                           id="no_of_decimal"
                                                                           name="no_of_decimal"
                                                                           value="{{ $currencyFormatSetting->no_of_decimal }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row border-top border-bottom m-2 pt-3 pb-3">
                                                            @lang('app.sample') - &nbsp;
                                                            <sapn id="formatted_currency">
                                                                {{ currencyFormatter(1234567.89) }}</sapn>
                                                        </div>

                                                        <div class="form-group">
                                                            <button id="saveCurrencyFormate" type="button"
                                                                    class="btn btn-success"><i
                                                                    class="fa fa-check"></i> @lang('app.save')</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- CURRENCY TAB --}}
                                {{-- LANGUAGE TAB --}}
                                <div class="tab-pane" id="language">
                                    @include('superadmin.language.index')
                                </div>
                                {{-- LANGUAGE TAB --}}
                                <div class="tab-pane" id="theme">
                                    <form class="form-horizontal ajax-form" id="theme-form" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <h4>@lang('menu.superAdminThemeSettings')</h4>
                                        <section class="mt-3 mb-5">
                                            <div class="row">
                                                <h6 class="col-md-12">@lang('modules.theme.subheadings.colorPallette')
                                                    <span type="button" id="resetSuperAdminColor"
                                                          class="btn badge badge-primary">@lang("app.reset")</span></h6>
                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.primaryColor')</label>
                                                        <input id="superadminPrimaryColor" type="text"
                                                               class="form-control color-picker"
                                                               name="superadmin[primary_color]"
                                                               value="{{ $superAdminThemeSetting->primary_color }}">
                                                        <div
                                                            style="background-color: {{ $superAdminThemeSetting->primary_color }}"
                                                            class=" border border-light">&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.secondaryColor')</label>
                                                        <input id="superadminSecondaryColor" type="text"
                                                               class="form-control color-picker"
                                                               name="superadmin[secondary_color]"
                                                               value="{{ $superAdminThemeSetting->secondary_color }}">
                                                        <div
                                                            style="background-color: {{ $superAdminThemeSetting->secondary_color }}"
                                                            class=" border border-light">&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.sidebarBgColor')</label>
                                                        <input id="superadminSidebarBgColor" type="text"
                                                               class="form-control color-picker"
                                                               name="superadmin[sidebar_bg_color]"
                                                               value="{{ $superAdminThemeSetting->sidebar_bg_color }}">
                                                        <div
                                                            style="background-color: {{ $superAdminThemeSetting->sidebar_bg_color }}"
                                                            class=" border border-light">&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.sidebarTextColor')</label>
                                                        <input id="superadminSidebarTextColor" type="text"
                                                               class="form-control color-picker"
                                                               name="superadmin[sidebar_text_color]"
                                                               value="{{ $superAdminThemeSetting->sidebar_text_color }}">
                                                        <div
                                                            style="background-color: {{ $superAdminThemeSetting->sidebar_text_color }}"
                                                            class="border border-light">&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.topbarTextColor')</label>
                                                        <input id="superadminTopbarTextColor" type="text"
                                                               class="form-control color-picker"
                                                               name="superadmin[topbar_text_color]"
                                                               value="{{ $superAdminThemeSetting->topbar_text_color }}">
                                                        <div
                                                            style="background-color: {{ $superAdminThemeSetting->topbar_text_color }}"
                                                            class="border border-light">&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->
                                            </div>
                                            <div class="row mb-3">
                                                <h6 class="col-md-12">@lang('modules.theme.subheadings.customCss') <span
                                                        type="button" id="resetSuperAdminCustomCss"
                                                        class="btn badge badge-primary">@lang("app.reset")</span></h6>
                                                <div class="col-md-12">
                                                    <div id="superadmin-custom-css">
                                                        @if(!$superAdminThemeSetting->custom_css)@lang('modules.theme.defaultCssMessage')@else{!!
                                            $superAdminThemeSetting->custom_css !!}@endif
                                                    </div>
                                                </div>
                                                <input id="superadmin-custom-input" type="hidden"
                                                       name="superadmin[custom_css]">
                                            </div>
                                        </section>
                                        <hr>
                                        <h4>@lang('menu.adminThemeSettings')</h4>
                                        <section class="mt-3 mb-5">
                                            <div class="row">
                                                <h6 class="col-md-12">@lang('modules.theme.subheadings.colorPallette')
                                                    <span type="button" id="resetAdminColor"
                                                          class="btn badge badge-primary">@lang("app.reset")</span></h6>
                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.primaryColor')</label>
                                                        <input id="adminPrimaryColor" type="text"
                                                               class="form-control color-picker"
                                                               name="administrator[primary_color]"
                                                               value="{{ $adminThemeSetting->primary_color }}">
                                                        <div
                                                            style="background-color: {{ $adminThemeSetting->primary_color }}"
                                                            class=" border border-light">&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.secondaryColor')</label>
                                                        <input id="adminSecondaryColor" type="text"
                                                               class="form-control color-picker"
                                                               name="administrator[secondary_color]"
                                                               value="{{ $adminThemeSetting->secondary_color }}">
                                                        <div
                                                            style="background-color: {{ $adminThemeSetting->secondary_color }}"
                                                            class=" border border-light">&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.sidebarBgColor')</label>
                                                        <input id="adminSidebarBgColor" type="text"
                                                               class="form-control color-picker"
                                                               name="administrator[sidebar_bg_color]"
                                                               value="{{ $adminThemeSetting->sidebar_bg_color }}">
                                                        <div
                                                            style="background-color: {{ $adminThemeSetting->sidebar_bg_color }}"
                                                            class=" border border-light">&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.sidebarTextColor')</label>
                                                        <input id="adminSidebarTextColor" type="text"
                                                               class="form-control color-picker"
                                                               name="administrator[sidebar_text_color]"
                                                               value="{{ $adminThemeSetting->sidebar_text_color }}">
                                                        <div
                                                            style="background-color: {{ $adminThemeSetting->sidebar_text_color }}"
                                                            class="border border-light">&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.topbarTextColor')</label>
                                                        <input id="adminTopbarTextColor" type="text"
                                                               class="form-control color-picker"
                                                               name="administrator[topbar_text_color]"
                                                               value="{{ $adminThemeSetting->topbar_text_color }}">
                                                        <div
                                                            style="background-color: {{ $adminThemeSetting->topbar_text_color }}"
                                                            class="border border-light">&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->
                                            </div>
                                            <div class="row mb-3">
                                                <h6 class="col-md-12">@lang('modules.theme.subheadings.customCss') <span
                                                        type="button" id="resetAdminCustomCss"
                                                        class="btn badge badge-primary">@lang("app.reset")</span></h6>
                                                <div class="col-md-12">
                                                    <div id="admin-custom-css">
                                                        @if(!$adminThemeSetting->custom_css)@lang('modules.theme.defaultCssMessage')@else{!!
                                            $adminThemeSetting->custom_css !!}@endif
                                                    </div>
                                                </div>
                                                <input id="admin-custom-input" type="hidden"
                                                       name="administrator[custom_css]">
                                            </div>
                                        </section>
                                        <hr>
                                        <h4>@lang('menu.customerThemeSettings')</h4>
                                        <section class="mt-3 mb-5">
                                            <div class="row">
                                                <h6 class="col-md-12">@lang('modules.theme.subheadings.colorPallette')
                                                    <span type="button" id="resetCustomerColor"
                                                          class="btn badge badge-primary">@lang("app.reset")</span></h6>
                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.primaryColor')</label>
                                                        <input id="customerPrimaryColor" type="text"
                                                               class="form-control color-picker"
                                                               name="customer[primary_color]"
                                                               value="{{ $customerThemeSetting->primary_color }}">
                                                        <div
                                                            style="background-color: {{ $customerThemeSetting->primary_color }}"
                                                            class=" border border-light">&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.secondaryColor')</label>
                                                        <input id="customerSecondaryColor" type="text"
                                                               class="form-control color-picker"
                                                               name="customer[secondary_color]"
                                                               value="{{ $customerThemeSetting->secondary_color }}">
                                                        <div
                                                            style="background-color: {{ $customerThemeSetting->secondary_color }}"
                                                            class=" border border-light">&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.sidebarBgColor')</label>
                                                        <input id="customerSidebarBgColor" type="text"
                                                               class="form-control color-picker"
                                                               name="customer[sidebar_bg_color]"
                                                               value="{{ $customerThemeSetting->sidebar_bg_color }}">
                                                        <div
                                                            style="background-color: {{ $customerThemeSetting->sidebar_bg_color }}"
                                                            class=" border border-light">&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.sidebarTextColor')</label>
                                                        <input id="customerSidebarTextColor" type="text"
                                                               class="form-control color-picker"
                                                               name="customer[sidebar_text_color]"
                                                               value="{{ $customerThemeSetting->sidebar_text_color }}">
                                                        <div
                                                            style="background-color: {{ $customerThemeSetting->sidebar_text_color }}"
                                                            class="border border-light">&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.topbarTextColor')</label>
                                                        <input type="text" class="form-control color-picker"
                                                               name="customer[topbar_text_color]"
                                                               value="{{ $customerThemeSetting->topbar_text_color }}">
                                                        <div
                                                            style="background-color: {{ $customerThemeSetting->topbar_text_color }}"
                                                            class="border border-light">&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->
                                            </div>
                                            <div class="row mb-3">
                                                <h6 class="col-md-12">@lang('modules.theme.subheadings.customCss') <span
                                                        type="button" id="resetCustomerCustomCss"
                                                        class="btn badge badge-primary">@lang("app.reset")</span></h6>
                                                <div class="col-md-12">
                                                    <div id="customer-custom-css">
                                                        @if(!$customerThemeSetting->custom_css)@lang('modules.theme.defaultCssMessage')@else{!!
                                            $customerThemeSetting->custom_css !!}@endif
                                                    </div>
                                                </div>
                                                <input id="customer-custom-input" type="hidden"
                                                       name="customer[custom_css]">
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <button id="save-theme" type="button" class="btn btn-success"><i
                                                            class="fa fa-check"></i> @lang('app.save')</button>
                                                </div>
                                            </div>
                                        </section>
                                        <hr>
                                    </form>
                                </div>
                                {{-- ADMIN-THEME TAB --}}

                            <!--tax-tab-pane -->
                                <div class="tab-pane" id="tax">
                                    @include('superadmin.tax-settings.index')
                                </div>
                                <!-- tax-tab-pane -->

                                {{-- EMAIL TAB --}}
                                <div class="tab-pane" id="email">
                                    <h4>@lang('app.email') @lang('menu.settings')</h4>
                                    <form class="form-horizontal ajax-form" id="email-form" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div id="alert">
                                            @if($smtpSetting->mail_driver =='smtp')
                                                @if($smtpSetting->verified)
                                                    <div
                                                        class="alert alert-success">{{__('messages.smtpSuccess')}}</div>
                                                @else
                                                    <div class="alert alert-danger">{{__('messages.smtpError')}}</div>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 ">
                                                <div class="form-group">
                                                    <label>@lang("modules.emailSettings.mailDriver")</label>
                                                    <div class="form-group">
                                                        <label class="radio-inline"> <input type="radio"
                                                                                            class="checkbox get-driver"
                                                                                            value="mail"
                                                                                            @if($smtpSetting->mail_driver == 'mail') checked
                                                                                            @endif
                                                                                            name="mail_driver"> @lang("modules.emailSettings.mail")
                                                        </label>
                                                        <label class="radio-inline pl-lg-2"> <input type="radio"
                                                                                                    class="checkbox get-driver"
                                                                                                    value="smtp"
                                                                                                    @if($smtpSetting->mail_driver == 'smtp') checked
                                                                                                    @endif
                                                                                                    name="mail_driver"> @lang("modules.emailSettings.smtp")
                                                        </label>
                                                    </div>
                                                </div>
                                                <div id="smtp_div" class="@if($smtpSetting->mail_driver == 'mail') d-none
                                            @endif">
                                                    <div class="form-group">
                                                        <label>@lang("modules.emailSettings.mailHost")</label>
                                                        <input type="text" name="mail_host" id="mail_host"
                                                               class="form-control form-control-lg"
                                                               value="{{ $smtpSetting->mail_host }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>@lang("modules.emailSettings.mailPort")</label>
                                                        <input type="text" name="mail_port" id="mail_port"
                                                               class="form-control form-control-lg"
                                                               value="{{ $smtpSetting->mail_port }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>@lang("modules.emailSettings.mailUsername")</label>
                                                        <input type="text" name="mail_username" id="mail_username"
                                                               class="form-control form-control-lg"
                                                               value="{{ $smtpSetting->mail_username }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label
                                                            class="control-label">@lang("modules.emailSettings.mailPassword")</label>
                                                        <input type="password" name="mail_password" id="mail_password"
                                                               class="form-control form-control-lg"
                                                               value="{{ $smtpSetting->mail_password }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label
                                                            class="control-label">@lang("modules.emailSettings.mailEncryption")</label>
                                                        <select class="form-control form-control-lg"
                                                                name="mail_encryption"
                                                                id="mail_encryption">
                                                            <option @if($smtpSetting->mail_encryption == 'tls') selected
                                                                @endif>
                                                                tls
                                                            </option>
                                                            <option @if($smtpSetting->mail_encryption == 'ssl') selected
                                                                @endif>
                                                                ssl
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="control-label">@lang("modules.emailSettings.mailFrom")</label>
                                                    <input type="text" name="mail_from_name" id="mail_from_name"
                                                           class="form-control form-control-lg"
                                                           value="{{ $smtpSetting->mail_from_name }}">
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="control-label">@lang("modules.emailSettings.mailFromEmail")</label>
                                                    <input type="text" name="mail_from_email" id="mail_from_email"
                                                           class="form-control form-control-lg"
                                                           value="{{ $smtpSetting->mail_from_email }}">
                                                </div>
                                                <div class="form-group">
                                                    <button id="save-email" type="button" class="btn btn-success"><i
                                                            class="fa fa-check"></i> @lang('app.save')</button>
                                                    <button id="send-test-email" type="button" class="btn btn-primary">
                                                        <i
                                                            class="fa fa-envelope"></i> @lang('app.sendTestEmail')
                                                    </button>
                                                </div>
                                            </div>
                                            <!--/span-->
                                        </div>
                                    </form>
                                </div>
                                {{-- EMAIL TAB --}}

                            <!-- Social login settings-->
                                <div class="tab-pane" id="social-login-settings">
                                    @include('superadmin.social-settings.index')
                                </div>
                                <!-- End Social login settings-->

                                {{-- SMS TAB --}}
                                <div class="tab-pane" id="sms-settings">
                                    @include('superadmin.sms-settings.index')
                                </div>
                                {{-- SMS TAB --}}
                                {{-- SEO TAB --}}
                                <div class="tab-pane" id="seo-settings">
                                    <h4>@lang('menu.seoSettings')</h4>
                                    <hr>
                                    <form class="form-horizontal ajax-form" id="seo-form" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">@lang('app.site')
                                                        @lang('app.description')</label>
                                                    <textarea name="seo_description" id="seo_description" cols="30"
                                                              class="form-control-lg form-control" rows="5">{{ ucwords($frontThemeSettings->seo_description) }}
                                            </textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">@lang('app.site')
                                                        @lang('app.keywords')</label>
                                                    <input type="text" class="form-control form-control-lg"
                                                           id="seo_keywords" name="seo_keywords" data-role="tagsinput"
                                                           value="{{ ucwords($frontThemeSettings->seo_keywords) }}"/>
                                                </div>
                                                <div class="form-group">
                                                    <button id="save-seo-settings" type="button"
                                                            class="btn btn-success"><i
                                                            class="fa fa-check"></i> @lang('app.save')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                {{-- SEO TAB --}}
                                {{-- CONTACT TAB --}}
                                <div class="tab-pane" id="contact-settings">
                                    <h4>@lang('menu.contactSettings')</h4>
                                    <hr>
                                    <form class="form-horizontal ajax-form" id="contact-form" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="contact_email"
                                                           class="control-label">@lang('app.contact')
                                                        @lang('app.email')</label>
                                                    <input type="text" class="form-control  form-control-lg"
                                                           id="contact_email" name="contact_email"
                                                           value="{{ $settings->contact_email }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <button id="save-contact-settings" type="button"
                                                            class="btn btn-success"><i class="fa fa-check"></i>
                                                        @lang('app.save')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                {{-- CONTACT TAB --}}
                                {{-- Map Configuration TAB --}}
                                <div class="tab-pane" id="map-configuration">
                                    <h4>@lang('menu.mapConfiguration') @lang('menu.settings')</h4>

                                    <hr>
                                    <form class="form-horizontal ajax-form" id="map-config-form" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h5 class="text-secondary">@lang('app.showMapOption')</h5>
                                                <div class="form-group">
                                                    <label class="control-label">@lang("app.allowMapOption")</label>
                                                    <br>
                                                    <label class="switch">
                                                        <input type="checkbox" name="map_option" id="map_option"
                                                               {{$settings->map_option == 'active'?'checked':''}} value="active">
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-12 {{$settings->map_option == 'deactive'?'d-none':''}}"
                                                 id="map_key_option">
                                                <div class="form-group">
                                                    <label for="map_key"
                                                           class="control-label">@lang('app.mapKey')</label>
                                                    <input type="text" class="form-control  form-control-lg"
                                                           id="map_key" name="map_key"
                                                           value="{{ $settings->map_key }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <button id="save-map-configuration" type="button"
                                                            class="btn btn-success"><i class="fa fa-check"></i>
                                                        @lang('app.save')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                {{-- Map Configuration TAB --}}


                                <div class="tab-pane" id="role-permission">
                                    @include('superadmin.role-permission.index')
                                </div>

                                <div class="tab-pane" id="ticket-settings">
                                    @include('superadmin.ticket-settings.index')
                                </div>

                                {{-- Google OAuth Configuration TAB --}}
                            <!-- /.tab-pane -->
                                <div class="tab-pane" id="googleCalendar">
                                    @include('superadmin.google.index')
                                </div>
                                {{-- Google OAuth Configuration TAB --}}
                                {{-- FREE-TRIAL TAB --}}
                                <div class="tab-pane" id="free-trial">
                                    <h4>@lang('menu.freeTrialSetting')</h4>
                                    <br>
                                    <form class="form-horizontal ajax-form" id="package-setting-form" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('modules.package.name') </label>
                                                    <input type="text" class="form-control" name="name"
                                                           value="{{$package->name}}" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('app.maxEmployees') </label>
                                                    <input type="number" class="form-control" name="max_employees"
                                                           value="{{$package->max_employees}}" autocomplete="off"
                                                           onkeypress="return isNumberKey(event)">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('app.maxDeals')</label>
                                                    <input onkeypress="return isNumberKey(event)" type="number"
                                                           class="form-control" name="max_deals" min="0"
                                                           value="{{$package->max_deals}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('app.maxServices')</label>
                                                    <input onkeypress="return isNumberKey(event)" type="number"
                                                           class="form-control" name="max_services" min="0"
                                                           value="{{$package->max_services}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('app.maxRoles')</label>
                                                    <input onkeypress="return isNumberKey(event)" type="number"
                                                           class="form-control" name="max_roles" min="0"
                                                           value="{{$package->max_roles}}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>@lang('app.numberofDays')</label>
                                                    <input onkeypress="return isNumberKey(event)" type="number"
                                                           class="form-control" name="no_of_days" min="0"
                                                           value="{{$package->no_of_days}}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>@lang('app.notificationBeforeDays')</label>
                                                    <input onkeypress="return isNumberKey(event)" type="number"
                                                           class="form-control" name="notify_before_days" min="0"
                                                           value="{{$package->notify_before_days}}">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>@lang('app.trialMessageShownOnFrontPage')</label>
                                                    <input type="text" class="form-control" name="trial_message"
                                                           value="{{$package->trial_message}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-3 mb-3">
                                                <div class="switch-div d-flex">
                                                    <label class="switch">
                                                        <input name="status" class="lang_status" type="checkbox"
                                                               value="active"
                                                               data-lang-id="2"
                                                               @if($package->status=='active') checked @endif>
                                                        <span class="slider round"></span>
                                                    </label>
                                                    <label class="switch-label"
                                                           id="switch-label-label">@lang('app.status')</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <br>
                                                <h5 class="text-uppercase">@lang('modules.package.selectModules')</h5>
                                                <br>
                                            </div>
                                            <div class="col-md-12">
                                                <input type="checkbox" id="select_all" name="select_all"
                                                       @if(sizeof($package_modules)=== sizeof($selected_package_modules))
                                                       checked
                                                    @endif>
                                                <label for="select_all"
                                                       class="checkbox-label">@lang('app.selectAll')</label>
                                                <hr>
                                            </div>
                                            @foreach ($package_modules as $package_module)
                                                <div class="col-md-2">
                                                    <input required @if(!is_null($selected_package_modules) &&
                                        in_array($package_module->name, $selected_package_modules)) checked
                                                           @endif type="checkbox" id="checkbox{{ $package_module->id }}"
                                                           name="package_modules[{{ $package_module->id }}]"
                                                           class="package_modules"
                                                           value="{{ $package_module->name }}">
                                                    <label for="checkbox{{ $package_module->id }}"
                                                           class="checkbox-label">
                                                        {{ $package_module->name }} </label>
                                                </div>
                                            @endforeach
                                            @foreach ($errors->all() as $error)
                                                <label>{{ $error }}</label>
                                            @endforeach
                                            <div class="col-md-12">
                                                <br><br><br>
                                                <div class="form-group">
                                                    <label for="name">@lang('app.description')</label>
                                                    <textarea name="description" id="description" cols="30"
                                                              class="form-control-lg form-control"
                                                              rows="4">{{$package->description}}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <button type="button" id="save-free-trial"
                                                            class="btn btn-success btn-light-round"><i
                                                            class="fa fa-check"></i>
                                                        @lang('app.save')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                {{-- FREE-TRIAL TAB --}}
                            </div>
                            {{-- tab-content --}}
                        </div>
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
    </div>
@endsection

@push('footer-js')
    <script src="{{ asset('/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('assets/ace/ace.js') }}" type="text/javascript" charset="utf-8"></script>
    <script src="{{ asset('js/bootstrap-tagsinput.js') }}"></script>

    <script>
        var langTable = '';
        $(document).ready(function () {

            // language table
            langTable = $('#langTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('superadmin.language-settings.index') !!}',
                language: languageOptions(),
                "fnDrawCallback": function (oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                order: [[1, 'ASC']],
                columns: [
                    {data: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'code', name: 'code'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', width: '20%'}
                ]
            });
        });

        $('body').on('click', '.edit-language', function () {
            var id = $(this).data('row-id');
            var url = '{{ route('superadmin.language-settings.edit', ':id')}}';
            url = url.replace(':id', id);

            $(modal_lg + ' ' + modal_heading).html('@lang('app.edit') @lang('menu.language')');
            $.ajaxModal(modal_lg, url);
        });

        $('body').on('click', '#updateLangForm', function () {
            const form = $('#editLangForm');
            var id = $(this).data('row-id');
            var url = '{{ route('superadmin.language-settings.update', ':id')}}';
            url = url.replace(':id', id);
            $.easyAjax({
                url: url,
                container: '#editLangForm',
                type: "PUT",
                redirect: true,
                data: form.serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        $(modal_lg).modal('hide');
                        langTable._fnDraw();

                        if ($('#lang-status').val() !== '{{ $language->status }}' || ($('#language_code').val() !== '{{ $language->language_code }}' && '{{ $language->language_code }}' === '{{ $settings->locale }}')) {
                            location.reload();
                        }
                    }
                }
            })
        });

        $('body').on('click', '#create-language', function () {
            var url = '{{ route('superadmin.language-settings.create') }}';

            $(modal_lg + ' ' + modal_heading).html('@lang('app.createNew') @lang('menu.language')');
            $.ajaxModal(modal_lg, url);
        });

        $('body').on('click', '#saveLangForm', function () {
            const form = $('#createLangForm');
            $.easyAjax({
                url: '{{route('superadmin.language-settings.store')}}',
                container: '#createLangForm',
                type: "POST",
                redirect: true,
                data: form.serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        $(modal_lg).modal('hide');
                        langTable._fnDraw();
                        if ($('#lang-status').val() == 'enabled') {
                            location.reload();
                        }
                    }
                }
            })
        });
        $('body').on('click', '#update-currency', function () {
            var id = $(this).data('row-id');
            var url = '{{ route('superadmin.currency-settings.update', ':id')}}';
            url = url.replace(':id', id);
            $.easyAjax({
                url: url,
                container: '#updateCurrency',
                type: "POST",
                data: $('#updateCurrency').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        window.location.reload();
                    }
                }
            })
        });


        $('body').on('click', '.delete-language-row', function () {
            var id = $(this).data('row-id');
            const lang = {!! $languages !!}.filter(language => language.id == id);

            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            }).then((willDelete) => {
                if (willDelete) {
                    var url = "{{ route('superadmin.language-settings.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                langTable._fnDraw();

                                if (lang[0].status == 'enabled') {
                                    langTable._fnDraw();
                                }
                            }
                        }
                    });
                }
            });
        });

        $('body').on('change', '.lang_status', function () {

            const id = $(this).data('lang-id');

            let url = '{{ route('superadmin.language-settings.changeStatus', ':id') }}'
            url = url.replace(':id', id);

            let status = '';
            if ($(this).is(':checked')) {
                status = 'enabled';
            } else {
                status = 'disabled';
            }

            $.easyAjax({
                url: url,
                type: 'POST',
                container: '#langTable',
                data: {
                    id: id,
                    status: status,
                    _method: 'PUT',
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.status == 'success') {
                        location.reload();
                    }
                }
            });
        });

    </script>

    {{--    @include('vendor.froiden-envato.update.update_script')--}}

    <script>
        $(function () {
            $('#nexmo_status').is(':checked') ? $('#nexmo-credentials').show() : $('#nexmo-credentials').hide();
            $('#msg91_status').is(':checked') ? $('#msg91-credentials').show() : $('#msg91-credentials').hide();
            $('#google_status').is(':checked') ? $('#google-credentials').show() : $('#google-credentials').hide();
            $('#facebook_status').is(':checked') ? $('#facebook-credentials').show() : $('#facebook-credentials').hide();

            $('body').on('click', '#v-pills-tab a', function (e) {
                e.preventDefault();
                $(this).tab('show');
                $("html, body").scrollTop(0);
            });

            // store the currently selected tab in the hash value
            $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
                e.preventDefault();
                var id = $(e.target).attr("href").substr(1);
                if (id == 'currencySettings' || id == 'currencyFormateSettings') {
                    id = 'currency';
                }

                if (id == 'ticketAgent' || id == 'ticketType' || id == 'ticketPriority' || id == 'ticketTemplate') {
                    id = 'ticket-settings';
                }
                window.location.hash = id;
            });

            // on load of the page: switch to the currently selected tab
            var hash = window.location.hash;
            $('#v-pills-tab a[href="' + hash + '"]').tab('show');
        });

        var superAdminCssEditor = ace.edit('superadmin-custom-css', {
            mode: 'ace/mode/css',
            theme: 'ace/theme/twilight'
        });

        var adminCssEditor = ace.edit('admin-custom-css', {
            mode: 'ace/mode/css',
            theme: 'ace/theme/twilight'
        });

        var customerCssEditor = ace.edit('customer-custom-css', {
            mode: 'ace/mode/css',
            theme: 'ace/theme/twilight'
        });

        function checkCurrencyCode(currency_code) {
            if (currency_code === 'INR') {
                return true;
            } else {
                return false;
            }
        }

        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

        $('.color-picker').colorpicker({
            format: 'hex'
        }).on('change', function (e) {
            $(this).siblings('div').css('background-color', e.value)
        })

        $('.offline-payment').change(function () {
            if ($(this).is(':checked')) {
                $('#offlinePayment').val(1);
            } else {
                $('#offlinePayment').val(0);
            }
        });

        function toggleRolePermission(elementBox) {
            $(elementBox).toggleClass('d-none');
        }

        function toggle(elementBox) {
            var elBox = $(elementBox);
            elBox.slideToggle();
        }

        $('.color-picker').colorpicker({
            format: 'hex'
        });

        // Start Tax Script
        taxTable = $('#taxTable').dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{!! route('superadmin.tax-settings.index') !!}',

            "fnDrawCallback": function (oSettings) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            order: [[1, 'ASC']],
            columns: [
                {data: 'DT_RowIndex'},
                {data: 'name', name: 'name'},
                {data: 'percent', name: 'percent'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', width: '20%'}
            ]
        });
        new $.fn.dataTable.FixedHeader(taxTable);

        $('body').on('click', '.add-tax', function () {
            var url = "{{ route('superadmin.tax-settings.create') }}";

            $(modal_default + ' ' + modal_heading).html('@lang('app.edit') @lang('app.tax')');
            $.ajaxModal(modal_default, url);
        });

        $('body').on('click', '.edit-tax', function () {
            var id = $(this).data('row-id');
            var url = "{{ route('superadmin.tax-settings.edit', ':id') }}";
            url = url.replace(':id', id);

            $(modal_default + ' ' + modal_heading).html('@lang('app.edit') @lang('app.tax')');
            $.ajaxModal(modal_default, url);
        });

        $('body').on('click', '.delete-tax', function () {
            var id = $(this).data('row-id');
            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            })
                .then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('superadmin.tax-settings.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.unblockUI();
                                    taxTable._fnDraw();
                                }
                            }
                        });
                    }
                });
        });
        // End Tax Script

        $('body').on('click', '#save-social-settings', function () {
            $.easyAjax({
                url: '{{route('superadmin.social-auth-settings.update', $socialCredentials->id)}}',
                container: '#social-login-form',
                type: "POST",
                file: true
            })
        });

        $('body').on('click', '#select_all', function () {
            if ($(this).prop("checked")) {
                $(".package_modules").prop("checked", true);
            } else {
                $(".package_modules").prop("checked", false);
            }
        });

        $('body').on('click', '#save-general', function () {
            $.easyAjax({
                url: '{{route('superadmin.settings.update', $settings->id)}}',
                container: '#general-form',
                type: "POST",
                file: true,
                success: function (response) {
                    console.log(response);
                    /*if (response.status === 'success') {
                        window.location.reload();
                    }*/
                },
                error: function (error) {
                    console.log(error);
                }
            })
        });

        $('body').on('click', '#save-currency', function () {
            $.easyAjax({
                url: '{{route('superadmin.currency-settings.store')}}',
                container: '#currency-form',
                type: "POST",
                data: $('#currency-form').serialize(),
                success: function (response) {
                    if (response.status === 'success') {
                        window.location.reload();
                    }
                }
            })
        });

        $('body').on('click', '#save-seo-settings', function () {
            $.easyAjax({
                url: '{{route('superadmin.add-seo-details')}}',
                container: '#seo-form',
                type: "POST",
                data: $('#seo-form').serialize()
            });
        });

        $('body').on('click', '#save-contact-settings', function () {
            $.easyAjax({
                url: '{{route('superadmin.save-contact-settings')}}',
                container: '#contact-form',
                type: "POST",
                data: $('#contact-form').serialize()
            });
        });


        $('body').on('change', '#map_option', function () {
            if ($(this).is(':checked')) {
                $('#map_key_option').removeClass('d-none')
            } else {
                $('#map_key_option').addClass('d-none')
            }
        });


        $('body').on('click', '#save-map-configuration', function () {
            $.easyAjax({
                url: '{{route('superadmin.save-map-configuration')}}',
                container: '#map-config-form',
                type: "POST",
                data: $('#map-config-form').serialize()
            });
        });

        $('body').on('change', '#google_calendar', function () {
            if ($(this).is(':checked')) {
                $('#google_calendar_config_option').removeClass('d-none')
            } else {
                $('#google_calendar_config_option').addClass('d-none')
            }
        });

        $('body').on('click', '#saveGoogleCalendarConfigForm', function () {
            $.easyAjax({
                url: '{{route('superadmin.saveGoogleCalendarConfig')}}',
                container: '#googleCalendarConfigForm',
                type: "POST",
                data: $('#googleCalendarConfigForm').serialize()
            });
        });

        $('body').on('click', '#save-free-trial', function () {
            $.easyAjax({
                url: '{{route('superadmin.freeTrialSetting', $package->id)}}',
                container: '#package-setting-form',
                type: "POST",
                data: $('#package-setting-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        location.reload();
                    }
                }
            })
        });

        $('body').on('click', '#save-sms-settings', function () {
            $.easyAjax({
                url: '{{route('superadmin.sms-settings.update', $smsSetting->id)}}',
                container: '#sms-setting-form',
                type: "POST",
                data: $('#sms-setting-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        location.reload();
                    }
                }
            })
        });

        $('body').on('click', '#save-theme', function () {
            $('#superadmin-custom-input').val(superAdminCssEditor.getValue());
            $('#admin-custom-input').val(adminCssEditor.getValue());
            $('#customer-custom-input').val(customerCssEditor.getValue());

            $.easyAjax({
                url: '{{route('superadmin.theme-settings.updateSettings')}}',
                container: '#theme-form',
                type: "POST",
                data: $('#theme-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        location.reload();
                    }
                }
            })
        });

        $('body').on('click', '.delete-currency', function () {
            var id = $(this).data('row-id');
            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            }).then((willDelete) => {
                if (willDelete) {
                    var url = "{{ route('superadmin.currency-settings.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                $('#currency-' + id).remove();
                            }
                        }
                    });
                }
            });
        });

        $('body').on('click', '.edit-currency', function () {
            var id = $(this).data('row-id');
            var url = '{{ route('superadmin.currency-settings.edit', ':id')}}';
            url = url.replace(':id', id);

            $(modal_lg + ' ' + modal_heading).html('@lang('app.edit') @lang('menu.currency')');
            $.ajaxModal(modal_lg, url);
        });

        $('body').on('click', '#save-email', function () {
            $.easyAjax({
                url: '{{route('superadmin.email-settings.update', $smtpSetting->id)}}',
                container: '#email-form',
                type: "POST",
                data: $('#email-form').serialize(),
                messagePosition: "inline",
                success: function (response) {
                    if (response.status == 'error') {
                        $('#alert').prepend('<div class="alert alert-danger">{{__('messages.smtpError')}}</div>')
                    } else {
                        $('#alert').show();
                    }
                }
            });
        });

        $('body').on('click', '#send-test-email', function () {
            var url = '{{route('superadmin.email-settings.sendTestEmailModal')}}';
            $(modal_default + ' ' + modal_heading).html('@lang('app.testEmail')');
            $.ajaxModal(modal_default, url);
        });

        $('body').on('click', '#sendTestEmailSubmit', function () {
            $.easyAjax({
                url: '{{route('superadmin.email-settings.sendTestEmail')}}',
                type: "GET",
                messagePosition: "inline",
                container: "#testEmail",
                data: $('#testEmail').serialize()

            });
        });

        $('body').on('click', '.get-driver', function () {
            if ($(this).val() == 'mail') {
                $('#smtp_div').addClass('d-none');
                $('#alert').hide();
            } else {
                $('#smtp_div').removeClass('d-none');
                $('#alert').show();
            }
        });

        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }

        $('body').on('click', '.payment-setting-page', function () {
            var url = "{{ route('superadmin.payment-settings.index') }} ";
            window.location.href = url;
        });

        $('body').on('click', '.front-setting-page', function () {
            var url = "{{ route('superadmin.front-settings.index') }} ";
            window.location.href = url;
        });

        /*$('body').on('click', '.update-app', function() {
{{--                var url = "{{ route('superadmin.update.index') }} ";--}}
        window.location.href = url;
    });*/

        // Change Colors using Reset Button
        function colorChange(element, value) {
            element.val(value);
            element.siblings('div').css('background-color', value);
        }

        $('body').on('click', '#resetSuperAdminColor', function () {
            colorChange($('#superadminPrimaryColor'), '#414552');
            colorChange($('#superadminSecondaryColor'), '#788AE2');
            colorChange($('#superadminSidebarBgColor'), '#FFFFFF');
            colorChange($('#superadminSidebarTextColor'), '#5C5C62');
            colorChange($('#superadminTopbarTextColor'), '#FFFFFF');
        });

        $('body').on('click', '#resetAdminColor', function () {
            colorChange($('#adminPrimaryColor'), '#414552');
            colorChange($('#adminSecondaryColor'), '#788AE2');
            colorChange($('#adminSidebarBgColor'), '#FFFFFF');
            colorChange($('#adminSidebarTextColor'), '#5C5C62');
            colorChange($('#adminTopbarTextColor'), '#FFFFFF');
        });

        $('body').on('click', '#resetCustomerColor', function () {
            colorChange($('#customerPrimaryColor'), '#414552');
            colorChange($('#customerSecondaryColor'), '#788AE2');
            colorChange($('#customerSidebarBgColor'), '#FFFFFF');
            colorChange($('#customerSidebarTextColor'), '#5C5C62');
            colorChange($('#customerTopbarTextColor'), '#FFFFFF');
        });

        // Add Default CSS using Reset Button
        $('body').on('click', '#resetSuperAdminCustomCss', function () {
            superAdminCssEditor.setValue('@lang('modules.theme.defaultCssMessage')');
        });

        $('body').on('click', '#resetAdminCustomCss', function () {
            adminCssEditor.setValue('@lang('modules.theme.defaultCssMessage')');
        });

        $('body').on('click', '#resetCustomerCustomCss', function () {
            customerCssEditor.setValue('@lang('modules.theme.defaultCssMessage')');
        });


        $('body').on('click', '#saveCurrencyFormate', function () {
            $.easyAjax({
                url: '{{route('superadmin.currency.formateSettingsUpdate')}}',
                container: '#currencyFormateForm',
                type: "POST",
                data: $('#currencyFormateForm').serialize(),
                success: function (response) {
                    if (response.status == "success") {
                        $.unblockUI();
                        location.reload();
                    }
                }
            });
        });
        $("body").on("change keyup", "#currency_position, #thousand_separator, #decimal_separator, #no_of_decimal", function () {
            let number = 1234567.89;
            let no_of_decimal = $('#no_of_decimal').val();
            let decimal_separator = $('#decimal_separator').val();
            let thousand_separator = $('#thousand_separator').val();
            let currency_position = $('#currency_position').val();
            let formatted_currency = currency_format(number, no_of_decimal, decimal_separator, thousand_separator, currency_position);
            $('#formatted_currency').html(formatted_currency);
        });

        function currency_format(number, decimals, dec_point, thousands_sep, currency_position) {
            // Strip all characters but numerical ones.
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var currency_symbol = '{{globalSetting()->currency->currency_symbol}}';
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            // number = dec_point == '' ? s[0] : s.join(dec);
            number = s.join(dec);
            switch (currency_position) {
                case 'left':
                    number = number + currency_symbol;
                    break;
                case 'right':
                    number = currency_symbol + number;
                    break;
                case 'left_with_space':
                    number = number + ' ' + currency_symbol;
                    break;
                case 'right_with_space':
                    number = currency_symbol + ' ' + number;
                    break;
                default:
                    number = currency_symbol + number;
                    break;
            }
            return number;
        }
    </script>

@endpush
