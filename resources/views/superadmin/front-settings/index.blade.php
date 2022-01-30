@extends('layouts.master')

@push('head-css')
    <link href="{{asset('assets/plugins/swal/sweetalert.css')}}" rel="stylesheet">
    <link href="{{asset('front/css/croppie.min.css')}}" rel="stylesheet">
    <style>
        .dropify-wrapper, .dropify-preview, .dropify-render img {
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
        #captcha-detail-modal .modal-dialog{
            height: 90%;
            max-width: 100%;
        }
        #captcha-detail-modal .modal-content{
            width: 600px;
            margin: 0 auto;
        }
        .modal.show{
            padding-right: 0px !important;
        }
        #v2_captcha_container {
            margin-bottom: 1%;
        }
        #save-btn-div {
            margin-top:2%;
        }
        #customCss {
            margin-left: 0.4%;
            margin-right: 0.4%;
        }
        .google_recaptcha_options label
        {
            margin-bottom: -0.5rem;
        }
    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-12 col-md-2 mb-4 mt-3 mb-md-0 mt-md-0">
            <a class="nav-link mb-2" href=" {{ route('superadmin.settings.index') }}">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> @lang('app.back')
            </a>
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                 <a class="nav-link active" href="#section-setting" data-toggle="tab">@lang('menu.sectionSetting')</a>
                 <a class="nav-link" href="#front-pages" data-toggle="tab">@lang('menu.pages')</a>
                 <a class="nav-link" href="#faq-settings" data-toggle="tab">@lang('menu.faqSettings')</a>
                 <a class="nav-link" href="#signup-note" data-toggle="tab">@lang('menu.signupNote')</a>
                 <a class="nav-link" href="#terms-note" data-toggle="tab">@lang('menu.TncNote')</a>
                 <a class="nav-link" href="#front-slider" data-toggle="tab">@lang('menu.frontSliderSettings')</a>
                 <a class="nav-link" href="#front-ratings" data-toggle="tab">@lang('menu.frontRatingsSettings')</a>
                 <a class="nav-link" href="#front-theme" data-toggle="tab">@lang('menu.frontThemeSettings')</a>
                 <a class="nav-link" href="#front-stores" data-toggle="tab">@lang('menu.popularStoresSettings')</a>
                 <a class="nav-link" href="#google-captcha-settings" data-toggle="tab">@lang('menu.googleCaptcha') @lang('menu.settings')</a>
                 <a class="nav-link" href="#front-widget-settings" data-toggle="tab">@lang('menu.front.widgetSetting')</a>
                 <a class="nav-link" href="#footer-setting" data-toggle="tab">@lang('menu.footerSettings')</a>
            </div>
        </div>
        <div class="col-12 col-md-10">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content">

                                {{-- SECTION SETTINGS --}}
                                <div class="active tab-pane" id="section-setting">
                                    <div class="row">
                                        <div class="col-md-12 table-responsive">
                                            <table class="table table-condensed">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>@lang('app.sectionName')</th>
                                                    <th>@lang('app.status')</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($sections as $section)
                                                        <tr>
                                                            <td> {{ $loop->iteration }} </td>
                                                            <td> {{ $section->name }} </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <label class="switch">
                                                                        <input type="checkbox" name="section_status" @if($section->status == 'active') checked @endif
                                                                        value="active" data-section-id="{{$section->id}}">
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                {{-- SECTION SETTINGS --}}

                                {{-- FRONT PAGES --}}
                                <div class="tab-pane" id="front-pages">
                                    @include('superadmin.page.index')
                                </div>
                                {{-- FRONT PAGES ENDS --}}

                                {{-- FAQ SETTINGS --}}
                                <div class="tab-pane" id="faq-settings">
                                    <div class="d-flex justify-content-center justify-content-md-end mb-3">
                                        <a href="javascript:;" id="create-faq" class="btn btn-rounded btn-primary mb-1 mr-2"> <i class="fa fa-plus"></i> @lang('app.createNew') </a>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 table-responsive">
                                            <table class="table table-condensed">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>@lang('app.faq.question')</th>
                                                    <th>@lang('app.faq.answer')</th>
                                                    <th>@lang('app.faq.language')</th>
                                                    <th class="text-right">@lang('app.faq.action')</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @forelse($frontFaqs as $key => $faq)
                                                    <tr id="faq-{{ $faq->faq_id }}">
                                                        <td>{{ ($key+1) }}</td>
                                                        <td>{{ $faq->question }}</td>
                                                        <td>{!! $faq->answer !!}</td>
                                                        <td>{{ $faq->language_name }}</td>
                                                        <td  class="text-right" style="width: 10%;">
                                                            <a href="javascript:;" data-faq-id="{{ $faq->faq_id }}" class="btn btn-primary btn-circle edit-faq" data-toggle="tooltip" data-original-title="@lang('app.edit')"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                            <a href="javascript:;" class="btn btn-danger btn-circle delete-faq" data-toggle="tooltip" data-faq-id="{{ $faq->faq_id }}" data-original-title="@lang('app.delete')"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr> <td colspan="5">@lang('app.noData')</td> </tr>
                                                @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- FAQ SETTINGS --}}


                                {{-- SIGNUP/REGISTRATION NOTE --}}
                                <div class="tab-pane" id="signup-note">
                                    <div class="row">
                                        <div class="col-md-12 table-responsive">
                                            <table class="table table-condensed">
                                                <thead>
                                                <tr>
                                                    <th>@lang('app.signUpNote')</th>
                                                    <th class="text-right">@lang('app.faq.action')</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                    <td>{{ $settings->sign_up_note }}</td>
                                                        <td class="text-right">
                                                            <a href="javascript:;" data-note-id="{{ $settings->id }}" class="btn btn-primary btn-circle edit-note" data-toggle="tooltip" data-original-title="@lang('app.edit')"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                {{-- SIGNUP/REGISTRATION NOTE --}}

                                {{-- TERM NOTE --}}
                                <div class="tab-pane" id="terms-note">
                                    <div class="row">
                                        <div class="col-md-12 table-responsive">
                                            <table class="table table-condensed">
                                                <thead>
                                                <tr>
                                                    <th>@lang('menu.TncNote')</th>
                                                    <th class="text-right">@lang('app.faq.action')</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                    <td>{{ $settings->terms_note }}</td>
                                                        <td class="text-right">
                                                            <a href="javascript:;" data-note-id="{{ $settings->id }}" class="btn btn-primary btn-circle edit-terms" data-toggle="tooltip" data-original-title="@lang('app.edit')"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                {{-- TERM NOTE --}}

                                {{-- FRONT SLIDER --}}
                                <div class="tab-pane" id="front-slider">
                                    @include('superadmin.front-slider.index')
                                </div>
                                {{-- FRONT SLIDER --}}

                                <!-- FRONT RATING SETTINGS -->
                                <div class="tab-pane" id="front-ratings">
                                    <h4>@lang('menu.frontRatingsSettings')</h4>
                                    <br>
                                    <form class="form-horizontal ajax-form" id="front-rating-setting-form" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12 ">
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        @lang('menu.frontRatingsSettingsNote')
                                                    </label>
                                                    <br>
                                                    <label class="switch">
                                                        <input type="checkbox" name="rating_option" id="rating_option"
                                                        @if($global->rating_status == 'active') checked @endif
                                                        value="active">
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <!--/span-->
                                        </div>
                                    </form>
                                </div>
                                <!-- END FRONT SETTINGS -->

                                {{-- FRONT THEME --}}
                                <div class="tab-pane" id="front-theme">
                                    <h4>@lang('menu.frontThemeSettings')</h4>
                                    <hr>
                                    <section class="mt-3 mb-3">
                                        <form class="form-horizontal ajax-form" id="front-theme-form" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="col-md-12 text-primary">@lang('app.front.title')</h6>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control "
                                                                name="front_title" value={{ $frontThemeSettings->title }}
                                                                >
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <h6 class="col-md-12 text-primary">@lang('modules.theme.subheadings.colorPallette') <span type="button" id="resetFrontColor" class="btn badge badge-primary">@lang("app.reset")</span></h6>
                                                    <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>@lang('modules.theme.primaryColor')</label>
                                                            <input id="frontPrimaryColor" type="text" class="form-control color-picker"
                                                                name="primary_color"
                                                                value="{{ $frontThemeSettings->primary_color }}">
                                                            <div class="border border-1"
                                                                style="background-color: {{ $frontThemeSettings->primary_color }}">&nbsp;
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>@lang('modules.theme.secondaryColor')</label>
                                                            <input id="frontSecondaryColor" type="text" class="form-control color-picker"
                                                                name="secondary_color"
                                                                value="{{ $frontThemeSettings->secondary_color }}">
                                                            <div class="border border-1"
                                                                style="background-color: {{ $frontThemeSettings->secondary_color }}">&nbsp;
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="col-md-12 text-primary">@lang('app.logo')</h6>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <input type="file" id="front-input-file-now"
                                                                           name="front_logo"
                                                                           accept=".png,.jpg,.jpeg" class="dropify"
                                                                           data-default-file="{{ $frontThemeSettings->logo_url }}"
                                                                    />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="col-md-12 text-primary">@lang('app.favicon')</h6>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <input type="file" id="front-input-file-now"
                                                                           name="favicon"
                                                                           accept=".png,.jpg,.jpeg" class="dropify"
                                                                           data-default-file="{{ $frontThemeSettings->favicon_url }}"
                                                                    />
                                                                </div>
                                                            </div>
                                                            <p class="text-danger">@lang('modules.favIconNote')</p>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>

                                            <div class="row mb-12" id="customCss">
                                                <h6 class="col-md-12 text-primary">@lang('modules.theme.subheadings.customCss') <span type="button" id="resetFrontCustomCss" class="btn badge badge-primary">@lang("app.reset")</span></h6>

                                                <div class="col-md-12">
                                                    <div id="front-custom-css">@if(!$frontThemeSettings->custom_css)@lang('modules.theme.defaultCssMessage')@else{!! $frontThemeSettings->custom_css !!}@endif</div>
                                                </div>

                                                <input id="front-custom-input" type="hidden" name="front_custom_css">
                                            </div>

                                            <div class="col-md-12" id="save-btn-div">
                                                <div class="form-group">
                                                    <button id="save-front-theme" type="button" class="btn btn-success">
                                                        <i class="fa fa-check"></i> @lang('app.save')</button>
                                                </div>
                                            </div>

                                        </form>
                                    </section>
                                </div>
                                {{-- FRONT THEME --}}

                                {{-- FRONT STORE --}}
                                <div class="tab-pane" id="front-stores">
                                    @include('superadmin.popular-stores.index')
                                </div>
                                {{-- FRONT STORE --}}

                                <!-- GOOGLE RECAPTCHA -->
                                <div class="tab-pane" id="google-captcha-settings">
                                    <h4>@lang('menu.googleCaptcha') @lang('menu.settings')</h4>
                                    <br>
                                    <form class="form-horizontal ajax-form" id="google-captcha-setting-form" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-12 ">
                                                <h5 class="text-info">@lang('menu.googleCaptcha') @lang('menu.credential') </h5>
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        @lang('menu.googleCaptcha') @lang('menu.status')
                                                    </label>
                                                    <br>
                                                    <label class="switch">
                                                        <input type="checkbox" @if($googleCaptchaSettings->status == 'active') class="google_captcha_status" @endif name="google_captcha_status" id="google_captcha_status"
                                                        @if($googleCaptchaSettings->status == 'active') checked @endif
                                                        value="active">
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>

                                                <div id="google-captcha-credentials">

                                                    <div class="form-group">
                                                        <label class="font-weight-bold">@lang('menu.chooseReCAPTCHAVersion')</label>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <label class="radio-inline"><input value="v2" id="v2" type="radio" name="version" @if ($googleCaptchaSettings->v2_status=="active") checked @endif >&nbsp;&nbsp; v2</label>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <label class="radio-inline"><input value="v3" id="v3" type="radio" name="version" @if ($googleCaptchaSettings->v3_status=="active") checked @endif>&nbsp;&nbsp; v3</label>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            @lang('menu.checkToApply')
                                                        </label>
                                                        <br>
                                                        <div class="row">
                                                            <div class="col-md-4 google_recaptcha_options mt-3">
                                                                <label class="switch">
                                                                    <input type="checkbox" name="vendor_registration_page" id="vendor_registration_page"
                                                                    @if($googleCaptchaSettings->vendor_page == 'active') checked @endif
                                                                    value="active">
                                                                    <span class="slider round"></span>
                                                                </label>
                                                                <label for="login_page" class="">@lang('menu.vendorRegistrationPage')</label>
                                                            </div>
                                                            <div class="col-md-4 google_recaptcha_options mt-3">
                                                                <label class="switch">
                                                                    <input type="checkbox" name="customer_registration_page" id="customer_registration_page"
                                                                    @if($googleCaptchaSettings->customer_page == 'active') checked @endif
                                                                    value="active">
                                                                    <span class="slider round"></span>
                                                                </label>
                                                                <label for="customer_registration_page" class="">@lang('menu.customerRegistrationPage')</label>
                                                            </div>
                                                            <div class="col-md-4 google_recaptcha_options mt-3">
                                                                <label class="switch">
                                                                    <input type="checkbox" name="login_page" id="login_page"
                                                                    @if($googleCaptchaSettings->login_page == 'active') checked @endif
                                                                    value="active">
                                                                    <span class="slider round"></span>
                                                                </label>
                                                                <label for="vendor_registration_page" class="">@lang('menu.loginPage')</label>
                                                            </div>
                                                        </div>
                                                        <br>
                                                    </div>

                                                    <div id="google_captcha_v3">
                                                        <div class="form-group">
                                                            <label>@lang('menu.site') @lang('menu.key')</label>
                                                            <input type="text" name="google_captcha3_site_key" id="v3_google_captcha_site_key"
                                                            class="form-control form-control-lg"
                                                            value="{{ $googleCaptchaSettings->v3_site_key }}">
                                                        </div>

                                                        <div class="form-group">
                                                            <label>@lang('menu.secret') @lang('menu.key')</label>
                                                            <input type="text" name="google_captcha3_secret" id="v3_google_captcha_secret"
                                                            class="form-control form-control-lg"
                                                            value="{{ $googleCaptchaSettings->v3_secret_key }}">
                                                        </div>

                                                        <div class="form-group">
                                                            <button type="button" class="btn btn-success" id="verify-v3"><i class="fa fa-check"></i> @lang('menu.verify')
                                                            </button>
                                                        </div>
                                                        <div class="col-lg-12" id="v3_captcha_container"></div>

                                                    </div>

                                                    <div id="google_captcha_v2">
                                                        <div class="form-group">
                                                            <label>@lang('menu.site') @lang('menu.key')</label>
                                                            <input type="text" name="google_captcha2_site_key" id="v2_google_captcha_site_key"
                                                            class="form-control form-control-lg"
                                                            value="{{ $googleCaptchaSettings->v2_site_key }}">
                                                        </div>

                                                        <div class="form-group">
                                                            <label>@lang('menu.secret') @lang('menu.key')</label>
                                                            <input type="text" name="google_captcha2_secret" id="v2_google_captcha_secret"
                                                            class="form-control form-control-lg"
                                                            value="{{ $googleCaptchaSettings->v2_secret_key }}">
                                                        </div>

                                                        <div class="col-lg-12" id="v2_captcha_container"></div>

                                                        <div class="form-group">
                                                            <button type="button" class="btn btn-success" id="verify-v2"><i class="fa fa-check"></i> @lang('menu.verify')
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <!--/span-->
                                        </div>
                                    </form>
                                </div>
                                {{-- GOOGLE RECAPTCHA --}}
                                {{-- FRONT WIDGET --}}
                                <div class="tab-pane" id="front-widget-settings">
                                    @include('superadmin.front-widget.index')
                                </div>
                                {{-- FOOTER SETTING --}}
                                <div class="tab-pane" id="footer-setting">
                                    <form id="footerSetting" class="ajax-form" method="post">
                                        @csrf
                                        @method('PUT')
                                        <h4>@lang('menu.footerSettings')</h4>
                                        <br>
                                        <h5 id="social-links">@lang('modules.frontCms.socialLinks')</h5>
                                        <hr>
                                        <span class="text-danger">@lang('modules.frontCms.socialLinksNote')</span><br><br>
                                        <div class="row">
                                            @forelse($footerSetting->social_links as $link)
                                                <div class="col-sm-12 col-md-3 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="{{ $link['name'] }}">
                                                            @lang('modules.frontCms.'.$link['name'])
                                                        </label>
                                                        <input
                                                                class="form-control"
                                                                id="{{ $link['name'] }}"
                                                                name="social_links[{{ $link['name'] }}]"
                                                                type="url"
                                                                value="{{ $link['link'] }}"
                                                                placeholder="@lang('modules.frontCms.enter'.ucfirst($link['name']).'Link')">
                                                    </div>
                                                </div>
                                            @empty

                                            @endforelse
                                        </div>
                                        <h5 id="footer-text">@lang('modules.frontCms.footerText')</h5>
                                        <hr>
                                        <div class="form-group">
                                            <input type="text" name="footer_text" class="form-control" value="{{ $footerSetting->footer_text }}">
                                        </div>
                                        <div class="form-group">
                                            <button id="save-footer-settings" type="button" class="btn btn-success">
                                            <i class="fa fa-check"></i> @lang('app.update')</button>
                                        </div>
                                    </form>
                                </div>
                                {{-- FOOTER SETTING --}}

                            </div>
                        </div>
                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
    </div>

{{--Default front custom CSS for Reset btn--}}
<textarea id="front-custom-css-default" class="d-none">

/* Coupon Box */
.coupon_code_box a {
    background-color: #ffcc00;
}
/* Deals Flag */
.featuredDealDetail .tag {
    background-color: #ffcc00;
}
/* Cart itme quantity number */
.cart-badge {
    background-color: #f72222;
}
</textarea>
{{--Default front custom CSS for Reset btn Ends--}}
@endsection

@push('footer-js')
    <script src="{{ asset('/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('assets/ace/ace.js') }}" type="text/javascript" charset="utf-8"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src="{{ asset('front/js/croppie.min.js') }}"></script>

    <script>

        $('body').on('change', 'input[type=checkbox][name=section_status]', function() {
            let id = $(this).data('section-id');
            let status = $(this).is(':checked') ? 'active' : 'inactive';
            let url = "{{ route('superadmin.change_section_status')}}";
            let token = "{{ csrf_token() }}";

            $.easyAjax({
                url: url,
                type: "GET",
                data: {id : id, status : status, '_token': token},
                success: function (response) {
                    if (response.status == 'success') {
                        location.reload();
                    }
                }
            })
        });

        $(document).on('click', '#verify-v2', function() {

            let captchaContainerV2 = null;
            let key = $('#v2_google_captcha_site_key').val();
            let secret = $('#v2_google_captcha_secret').val();

            if(key === '' || secret ==='') {
                swal({ title: "Error..!", icon: 'warning', text: '@lang("errors.reCaptchaWarning")', });
                return false;
            }

            try {
                captchaContainer = grecaptcha.render('v2_captcha_container', {
                    'sitekey' : key,
                    'callback' : function(response) {
                        if(response) {
                            saveForm();
                        }
                    },
                    'error-callback': function() {
                        errorMsg();
                    }
                });
            } catch (error) {
                errorMsg();
            }
        });

        $(document).on('click', '.google_captcha_status', function() {
            saveForm();
        });

        $(document).on('click', '#verify-v3', function() {
            let key = $('#v3_google_captcha_site_key').val();
            let secret = $('#v3_google_captcha_secret').val();
            var url = '{{ route('superadmin.google-captcha-settings.index')}}?key='+key;

            if(key === '' || secret === '') {
                swal({ title: "Error..!", icon: 'warning', text: '@lang("errors.reCaptchaWarning")', });
                return false;
            }

            var url = url;
            $.ajaxModal(modal_lg, url);
        });

        function saveForm() {
            var url = "{{route('superadmin.google-captcha-settings.update', $googleCaptchaSettings->id)}}";

            $.easyAjax({
                url: url,
                container: '#google-captcha-setting-form',
                type: "POST",
                data: $('#google-captcha-setting-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        location.reload();
                    }
                }
            })
        }

        function errorMsg() {
            var form = $("#google-captcha-setting-form");
            var checkedValue = form.find("input[name=version]:checked").val();

            if(checkedValue == 'v3') {
                let msg = `<div class="alert alert-danger" role="alert"><i class="fa fa-info-circle"></i>
                Unexpected error occured.
                </div>`;
                $('#portlet-body').html(msg);
                $('#portlet-body').attr('data-error', true);
                $('#save-method').hide();
                return false;
            }

            swal({
                title: "Error..!",
                text: "@lang('errors.invalidReCaptcha')",
                icon: 'warning',
                showCancelButton: false,
                focusConfirm: false,
                confirmButtonText: "Ok",
                customClass: {
                    confirmButton: 'btn btn-primary mr-3',
                    cancelButton: 'btn btn-secondary'
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false,
            }).then((willDelete) => {
                if (willDelete) {
                    location.reload();
                }
            });
        }

        var table = langTable = '';

    $(document).ready(function() {
        // pages table
        table = $('#frontWidgetTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('superadmin.front-widget.index') !!}',
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                order: [[0, 'DESC']],
                columns: [
                    { data: 'DT_RowIndex'},
                    { data: 'name', name: 'name' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', width: '20%' }
                ]
            });
            new $.fn.dataTable.FixedHeader( table );

        });


        $('body').on('click', '#create-widget', function() {
            var url = '{{ route('superadmin.front-widget.create') }}';

            $(modal_lg + ' ' + modal_heading).html('@lang('app.createNew') @lang('menu.widget')');
            $.ajaxModal(modal_lg, url);
        });

        $('body').on('click', '#rating_option', function() {
            const form = $('#front-rating-setting-form');

            $.easyAjax({
                url: '{{route('superadmin.ratings.store')}}',
                container: '#front-rating-setting-form',
                type: "POST",
                redirect: true,
                data: form.serialize(),
                success: function (response) {
                    if(response.status == 'success'){
                        //
                    }
                }
            })
        });

        $('body').on('click', '#saveWidgetForm', function() {
            const form = $('#createWidgetForm');

            $.easyAjax({
                url: '{{route('superadmin.front-widget.store')}}',
                container: '#createWidgetForm',
                type: "POST",
                redirect: true,
                data: form.serialize(),
                success: function (response) {
                    if(response.status == 'success'){
                        window.location.reload();
                    }
                }
            })
        });

        $('body').on('click', '.edit-widget', function() {
            var id = $(this).data('widget-id');
            var url = '{{ route('superadmin.front-widget.edit', ':id') }}';
            url = url.replace(':id', id);

            $(modal_lg + ' ' + modal_heading).html('@lang('app.edit') @lang('menu.widget')');
            $.ajaxModal(modal_lg, url);
        });

        $('body').on('click', '#updateWidgetForm', function() {
            const form = $('#editWidgetForm');
            var id = $(this).data('row-id');
            var url = '{{ route('superadmin.front-widget.update', ':id')}}';
            url = url.replace(':id', id);
            $.easyAjax({
                url: url,
                container: '#editWidgetForm',
                type: "POST",
                redirect: true,
                data: form.serialize(),
                success: function (response) {
                    if(response.status == 'success'){
                        window.location.reload();
                    }
                }
            })
        });

        $('body').on('click', '.delete-widget', function() {
            var id = $(this).data('widget-id');
            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            }).then((willDelete) => {
                if (willDelete) {
                    var url = "{{ route('superadmin.front-widget.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                location.reload();
                            }
                        }
                    });
                }
            });
        });

        $('body').on('click', '#create-faq', function() {
            var url = '{{ route('superadmin.front-faq.create') }}';

            $(modal_lg + ' ' + modal_heading).html('@lang('app.createNew') @lang('menu.faq')');
            $.ajaxModal(modal_lg, url);
        });
        $('body').on('click', '#saveFaqForm', function() {
        const form = $('#createFaqForm');

        $.easyAjax({
            url: '{{route('superadmin.front-faq.store')}}',
            container: '#createFaqForm',
            type: "POST",
            redirect: true,
            data: form.serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    });
        $('body').on('click', '.edit-faq', function() {
            var id = $(this).data('faq-id');

            var url = '{{ route('superadmin.front-faq.edit', ':id') }}';
            url = url.replace(':id', id);

            $(modal_lg + ' ' + modal_heading).html('@lang('app.edit') @lang('menu.faq')');
            $.ajaxModal(modal_lg, url);
        });

        $('body').on('click', '#updateFaqForm', function() {
        const form = $('#editFaqForm');
        var id = $(this).data('row-id');
        var url = '{{ route('superadmin.front-faq.update', ':id')}}';
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            container: '#editFaqForm',
            type: "POST",
            redirect: true,
            data: form.serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    });

        $('body').on('click', '.edit-note', function() {
            $(modal_lg + ' ' + modal_heading).html('@lang('app.edit')');
            $.ajaxModal(modal_lg, "{{ route('superadmin.editNote') }}");
        });

        $('body').on('click', '.edit-terms', function() {
            $(modal_lg + ' ' + modal_heading).html('@lang('app.edit')');
            $.ajaxModal(modal_lg, "{{ route('superadmin.editTerms') }}");
        });

        $('body').on('click', '.delete-faq', function() {
            var id = $(this).data('faq-id');
            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            }).then((willDelete) => {
                if (willDelete) {
                    var url = "{{ route('superadmin.front-faq.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                location.reload();
                            }
                        }
                    });
                }
            });
        });

        $(function () {
            $('#google_captcha_status').is(':checked') ? $('#google-captcha-credentials').show() : $('#google-captcha-credentials').hide();
            '{{ $googleCaptchaSettings->v2_status }}' === 'active' ? $('#google_captcha_v2').show() : $('#google_captcha_v2').hide();
            '{{ $googleCaptchaSettings->v3_status }}' === 'active' ? $('#google_captcha_v3').show() : $('#google_captcha_v3').hide();

            $('body').on('click', 'input[type="radio"]', function() {
                if($(this).attr('id') == 'v2') {
                        $('#google_captcha_v2').show();
                        $('#google_captcha_v3').hide();
                }
                else {
                    $('#google_captcha_v3').show();
                    $('#google_captcha_v2').hide();
                }
            });

            $('input[type=checkbox][name=google_captcha_status]').change(function() {
                $('#google-captcha-credentials').toggle();
            });

            $('body').on('click', '#v-pills-tab a', function(e) {
                e.preventDefault();
                $(this).tab('show');
                $("html, body").scrollTop(0);
            });

            // store the currently selected tab in the hash value
            $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
                var id = $(e.target).attr("href").substr(1);
                window.location.hash = id;
            });

            // on load of the page: switch to the currently selected tab
            var hash = window.location.hash;
            $('#v-pills-tab a[href="' + hash + '"]').tab('show');
        });

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

        var frontCssEditor = ace.edit('front-custom-css', {
            mode: 'ace/mode/css',
            theme: 'ace/theme/twilight'
        });

        $('body').on('click', '#save-front-theme', function() {
            $('#front-custom-input').val(frontCssEditor.getValue());
            $.easyAjax({
                url: '{{route('superadmin.front-theme-settings.update', $frontThemeSettings->id)}}',
                container: '#front-theme-form',
                type: "POST",
                file: true,
                success: function (response) {
                    if (response.status == 'success') {
                        location.reload();
                    }
                }
            })
        });

        $('body').on('click', '#save-footer-settings', function() {
            $.easyAjax({
                url: "{{ route('superadmin.front-settings.update', $footerSetting->id) }}",
                container: '#footerSetting',
                type: 'POST',
                data: $('#footerSetting').serialize()
            })
        })

        $('body').on('change', '#carousel-images', function() {
            $.easyAjax({
                url: '{{route('superadmin.front-theme-settings.store')}}',
                container: '#theme-carousel-form',
                type: "POST",
                file: true,
                success: function (response) {
                    $('#carousel-image-gallery').html(response.view);
                }
            });
        });

        $('body').on('click', '.delete-carousel-row', function() {
            var id = $(this).attr('id');
            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            }, function() {
                var url = "{{ route('superadmin.front-theme-settings.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            $('#carousel-image-gallery').html(response.view);
                        }
                    }
                })
            })
        });

        var table = langTable = '';

        $(document).ready(function() {
            // pages table
            table = $('#myTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('superadmin.pages.index') !!}',
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                order: [[0, 'DESC']],
                columns: [
                    { data: 'DT_RowIndex'},
                    { data: 'title', name: 'title' },
                    { data: 'slug', name: 'slug' },
                    { data: 'action', name: 'action', width: '20%' }
                ]
            });
            new $.fn.dataTable.FixedHeader( table );

            $('body').on('click', '.edit-page', function () {
                var slug = $(this).data('slug');
                var url = '{{ route('superadmin.pages.edit', ':slug')}}';
                url = url.replace(':slug', slug);

                $(modal_lg + ' ' + modal_heading).html('@lang('app.edit') @lang('menu.page')');
                $.ajaxModal(modal_lg, url);
            });

            $('body').on('click', '#create-page', function () {
                var url = '{{ route('superadmin.pages.create') }}';

                $(modal_lg + ' ' + modal_heading).html('@lang('app.createNew') @lang('menu.page')');
                $.ajaxModal(modal_lg, url);
            });


            $('body').on('click', '.delete-row', function(){
                var id = $(this).data('row-id');
                swal({
                    icon: "warning",
                    buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title: "@lang('errors.areYouSure')",
                    text: "@lang('errors.deleteWarning')",
                }).then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('superadmin.pages.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.unblockUI();
                                    table._fnDraw();
                                    location.reload();
                                }
                            }
                        });
                    }
                });
            });

        });

        var table = langTable = '';
        var sliderTable;

        $(document).ready(function() {
            // front slider table
            sliderTable = $('#sliderTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('superadmin.front-slider.index') !!}',
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                order: [[0, 'DESC']],
                columns: [
                    { data: 'DT_RowIndex'},
                    { data: 'image', name: 'image' },
                    { data: 'have_content', name: 'haveContent' },
                    { data: 'action', name: 'action' }
                ]
            });
            new $.fn.dataTable.FixedHeader( sliderTable );

            $('body').on('click', '#create-slider', function () {
                var url = '{{ route('superadmin.front-slider.create') }}';

                $(modal_lg + ' ' + modal_heading).html('@lang('app.createNew') @lang('menu.slider')');
                $.ajaxModal(modal_lg, url);
            });

            $('body').on('click', '.edit-slider', function () {
                var id = $(this).data('id');
                var url = '{{ route('superadmin.front-slider.edit', ':id')}}';
                url = url.replace(':id', id);

                $(modal_lg + ' ' + modal_heading).html('@lang('app.edit') @lang('menu.slider')');
                $.ajaxModal(modal_lg, url);
            });

            $('body').on('click', '.delete-slider', function(){
                var id = $(this).data('row-id');
                swal({
                    icon: "warning",
                    buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title: "@lang('errors.areYouSure')",
                    text: "@lang('errors.deleteWarning')",
                }).then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('superadmin.front-slider.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.unblockUI();
                                    sliderTable._fnDraw();
                                }
                            }
                        });
                    }
                });
            });

        });

        var table = langTable = '';

        $(document).ready(function() {
            table = $('#storeTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('superadmin.popular-stores.index') !!}',
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                order: [[0, 'DESC']],
                columns: [
                    { data: 'DT_RowIndex'},
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action' }
                ]
            });
            new $.fn.dataTable.FixedHeader( table );

            $('body').on('click', '#add-stores', function () {
                var url = '{{ route('superadmin.popular-stores.create') }}';

                $(modal_lg + ' ' + modal_heading).html('@lang('app.createNew') @lang('menu.slider')');
                $.ajaxModal(modal_lg, url);
            });


            $('body').on('click', '#savePopularStoreForm', function() {
                const form = $('#addPopularStoreForm');
                $.easyAjax({
                    url: '{{route('superadmin.popular-stores.store')}}',
                    container: '#addPopularStoreForm',
                    type: "POST",
                    file:true,
                    redirect: true,
                    data: form.serialize(),
                    success: function (response) {
                        if(response.status == 'success'){
                            $(modal_lg).modal('hide');
                            table._fnDraw();
                        }
                    }
                })
            });

            $('body').on('click', '.delete-store', function(){
                var id = $(this).data('row-id');
                swal({
                    icon: "warning",
                    buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title: "@lang('errors.areYouSure')",
                    text: "@lang('errors.deleteWarning')",
                }).then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('superadmin.popular-stores.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.unblockUI();
                                    table._fnDraw();
                                }
                            }
                        });
                    }
                });
            });

        });

        $('body').on('click', '#savePageForm', function () {
        const form = $('#createPageForm');
        $.easyAjax({
            url: '{{route('superadmin.pages.store')}}',
            container: '#createPageForm',
            type: "POST",
            redirect: true,
            data: form.serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    $(modal_lg).modal('hide');
                    location.reload();
                }
            }
        })

    });
    $('body').on('click', '#updatePageForm', function () {
        const form = $('#editPageForm');
        var id = $(this).data('row-id');
            var url = '{{ route('superadmin.pages.update', ':id')}}';
            url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            container: '#editPageForm',
            type: "PUT",
            redirect: true,
            data: form.serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    $(modal_lg).modal('hide');
                    location.reload();
                }
            }
        })
    });

    // Change Colors using Reset Button
    function colorChange(element,value) {
        element.val(value);
        element.siblings('div').css('background-color', value);
    }

    $('body').on('click', '#resetFrontColor', function() {
        colorChange($('#frontPrimaryColor'),'#00C1CF');
        colorChange($('#frontSecondaryColor'),'#373737');
    });

    // Add Default CSS using Reset Button
    $('body').on('click', '#resetFrontCustomCss', function() {
                frontCssEditor.setValue($('#front-custom-css-default').val());
    });
    </script>

{{--    @include('vendor.froiden-envato.update.update_script')--}}
@endpush
