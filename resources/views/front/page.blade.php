@extends('layouts.front')

@push('styles')
<style>
    .all_deals_section {
        padding: 40px
    }
    .form_wrapper {
        padding: 96px 60px 52px;
        width: 589px;
        border-radius: 15px;
        border: solid 2px #eef1f5;
        background-color: #fff;
        margin-top: 60px;
    }
    .form_wrapper span.form_icon {
        width: 122px;
        height: 122px;
        border-radius: 100%;
        background-color: var(--primary-color);
        position: absolute;
        top: -65px;
        margin: 0 auto;
        left: 0;
        right: 0;
        display: inline-grid;
    }
    .form_wrapper span.form_icon i {
        font-size: 35px;
        color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
@endpush

@section('content')

<!-- BREADCRUMB START -->
<section class="breadcrumb_section">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col-md-5">
                <h1 class="mb-0">{{ $page->title }}</h1>
            </div>
            <div class="col-lg-5 col-md-7">
                <nav>
                    <ol class="breadcrumb mb-0 justify-content-center">
                        <li class="breadcrumb-item"><a href="/">@lang('app.home')</a></li>
                        <li class="breadcrumb-item active"><span>{{ $page->title }}</span></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>
<!-- BREADCRUMB END -->

<section class="all_deals_section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-11 form_wrapper mx-auto position-relative">
                @if ($page->id == 2)
                    <form class="contact-form" id="contact_form" method="post" action="">
                        @csrf
                        <span class="form_icon"><i class="zmdi zmdi-email"></i></span>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>@lang('front.name') :</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter Your Name...">
                            </div>
                            <div class="form-group col-md-12">
                                <label>@lang('front.registration.email') :</label>
                                <input type="email" name="email" class="form-control" placeholder="Enter Your Email...">
                                <small id="emailHelp" class="form-text text-muted">@lang('front.emailHelp')</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('front.message') :</label>
                            <textarea name="details" class="form-control" rows="5"
                                placeholder="Enter Your Message..."></textarea>
                        </div>
                        <div class="form-group col-mb-12 mb-0 text-center">
                            <button type="button" name="submit" class="contactSubmitButton">
                                <i class="fa fa-send"></i>@lang('app.submit')
                            </button>
                        </div>
                    </form>
                @else
                <div class="col-md-12 text-left">
                    {!! $page->content !!}
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection

@push('footer-script')
<script>

    $('body').on('click', '.contactSubmitButton', function() {
         $.easyAjax({
            url: '{{ route('front.contact') }}',
            type: 'POST',
            container: '#contact_form',
            formReset: true,
            data: $('#contact_form').serialize(),
            blockUI: false,
            disableButton: true,
            buttonSelector: ".contactSubmitButton",
        })
    });

    $('body').on('keypress', '#contact_form input,#contact_form textarea', function(e) {
        $(this).siblings('.invalid-feedback').remove();
    })
</script>
@endpush
