<style>
    #coupon_detail{
        display: none;
        max-height: 200px;
        overflow: auto;
        scroll-margin:10px;
    }
</style>

{{-- MODAL SECTION --}}
    <div id="latest_coupon_modal_container">
        <div class="modal-background">
            <div class="modal">
                <div class="coupon_modal_header d-md-flex justify-content-center align-items-center position-relative">
                    <span class="close_coupon_modal">&times;</span>
                    <div class="col-md-3 coupon_logo">
                        <img src="{{asset('front/images/pixel.gif')}}" data-src="{{ $frontThemeSettings->logo_url }} " width="100%" alt="Logo" />
                    </div>
                    <div class="col-md-9 coupon_heading text-left">
                        <h2 class="mb-0" id="coupon_company">
                            {{ $settings->company_name }}
                        </h2>
                        <p id="coupon_title">@lang('app.couponTitle')</p>
                    </div>
                </div>
                <div class="coupon_modal_detail justify-content-center align-items-center">
                    <div class="active_code mb-3 d-lg-flex d-md-flex d-block justify-content-center align-items-center">
                        <p>@lang('app.dealActivated')</p>
                        <div class="input-group ml-0 ml-lg-4 ml-md-4">
                            <input type="text" name="coupon" class="form-control" placeholder="Apply Coupon" id="coupon_code">
                            <div class="input-group-prepend">
                                <button id="coupon_code_copy_btn" type="button" class="btn btn-sm input-group-text">@lang('app.copy')</button>
                            </div>
                        </div>
                    </div>
                    <div class="visit_logo_company">
                        <a href="javascript:;" class="detail_button" id="detail_button">@lang('app.showDetail')</a>
                        <p id="coupon_detail" class="coupon_detail text-justify"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- MODAL SECTION --}}
