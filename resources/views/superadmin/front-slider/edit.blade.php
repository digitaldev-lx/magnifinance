<style>
    .dropify-wrapper, .dropify-preview, .dropify-render img {
        background-color: var(--sidebar-bg) !important;
    }
    .d-none {
        display: none;
    }
    .mt3 {
        margin-top:3%;
    }
</style>

<div class="modal-header">
    <h5>@lang('menu.frontSliderSettings')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <section class="mt-3 mb-3">
        <form class="form-horizontal ajax-form" id="editFrontSliderForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <input type="hidden" id="slider_id" name="slider_id" @if(!is_null($frontSlide)) value="{{$frontSlide->id}}" @else @endif>
                <div class="col-md-12">
                    <!-- text input -->
                    <div class="form-group">
                        <h6 class="col-md-12 text-primary">@lang('app.image')</h6>
                        <div class="card">
                            <div class="card-body">
                                <input type="file" id="image" class="dropify" name="image"
                                    accept=".png,.jpg,.jpeg" data-default-file="{{ $frontSlide->image_url }}"/>
                            </div>
                            <div id="uploaded-image" class="d-none"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="col-md-12 text-primary">@lang('app.haveContent') ?</h6>
                        <div class="form-group">
                            <div class="card">
                                <div class="card-body mt3">
                                    <input type="radio" id="content_yes" name="have_content" value="yes" @if($frontSlide->have_content == 'yes') checked @endif>
                                    <label for="content_yes"><h6 class="">@lang('app.yes')</h6></label>&nbsp;&nbsp;&nbsp;
                                    <input type="radio" id="content_no" name="have_content" value="no" @if($frontSlide->have_content == 'no') checked @endif>
                                    <label for="content_no"> <h6 class="">@lang('app.no')</h6></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 slider_contents @if($frontSlide->have_content == 'no') d-none @endif">
                        <h6 class="col-md-6 text-primary">@lang('app.contentAlignment')</h6>
                        <div class="form-group">
                            <div class="card">
                                <div class="card-body mt3">
                                    <input type="radio" id="contentAlignment" name="content_alignment" value="left" @if($frontSlide->content_alignment == 'left') checked @endif>
                                    <label for="contentAlignment"> <h6 class="">@lang('app.left')</h6></label>&nbsp;&nbsp;&nbsp;
                                    <input type="radio" id="contentAlignment1" name="content_alignment" value="right" @if($frontSlide->content_alignment == 'right') checked @endif>
                                    <label for="contentAlignment1">  <h6 class="">@lang('app.right')</h6></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row slider_content_form @if($frontSlide->have_content == 'no') d-none @endif">
                    <div class="col-md-12">
                        <h6 class="col-md-6 text-primary">@lang('app.subHeading')</h6>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-lg" name="subheading" id="subheading" value="{{ $frontSlide->heading }}">
                            </div>
                    </div>
                    <div class="col-md-12">
                        <h6 class="col-md-6 text-primary">@lang('app.heading')</h6>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-lg" name="heading" id="heading" value="{{ $frontSlide->heading }}">
                            </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>@lang('app.page') @lang('app.content')</label>
                            <textarea name="slider_content" id="slider_content" cols="30" class="form-control-lg form-control"
                                rows="4">{{$frontSlide->content}}</textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="col-md-6 text-primary">@lang('app.openIn')</h6>
                        <div class="form-group mt3">
                            <div class="card">
                                <div class="card-body">
                                    <input type="radio" id="sameTab" name="open_tab" value="current" @if($frontSlide->open_tab == 'current') checked @endif>
                                    <label for="sameTab"> <h6 class="">@lang('app.current') @lang('app.tab')</h6></label>&nbsp;&nbsp;&nbsp;
                                    <input type="radio" id="newTab" name="open_tab" value="new" @if($frontSlide->open_tab == 'new') checked @endif>
                                    <label for="newTab">  <h6 class="">@lang('app.new') @lang('app.tab')</h6></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="col-md-6 text-primary">@lang('app.actionButton')</h6>
                        <div class="form-group mt3">
                            <div class="card">
                                <div class="card-body">
                                    <input type="radio" id="custom_yes" name="actionButton" value="login" @if($frontSlide->action_button == 'login') checked @endif>
                                    <label for="custom_yes"> <h6 class="">@lang('app.login')</h6></label>&nbsp;&nbsp;&nbsp;
                                    <input type="radio" id="custom_no" name="actionButton" value="custom" @if($frontSlide->action_button != 'login') checked @endif>
                                    <label for="custom_no">  <h6 class="">@lang('app.daterangepicker.custom')</h6></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 customData @if($frontSlide->action_button == 'login') d-none @endif">
                        <div class="row">`
                            <div class="col-md-6">
                                <h6 class="col-md-6 text-primary">@lang('app.customLabel') ?</h6>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-lg" name="custom_label" id="custom_label" value="{{ $frontSlide->action_button }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="col-md-6 text-primary">@lang('app.customUrl')</h6>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-lg" name="url" id="custom_url" value="{{ $frontSlide->url }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="updateFrontSliderForm" onclick="updateFrontSlider()" data-row-id="{{ $frontSlide->id }}" class="btn btn-success btn-light-round"><i
        class="fa fa-check"></i> @lang('app.submit')</button>
</div>

<script>

$uploadCrop = $('#uploaded-image').croppie({
        enableExif: true,
        viewport: {
            width: 500,
            height: 200,
            type: 'rectangle'
        },
        original: {
            width: 1920,
            height: 495,
        },
        boundary: {
            width: 774,
            height: 300
        }
    });

    $('body').on('change', '#image', function() {
        $('#uploaded-image').removeClass('d-none');
        var reader = new FileReader();
        reader.onload = function (e) {
            $uploadCrop.croppie('bind', {
                url: e.target.result
            }).then(function(){
                console.log('jQuery bind complete');
            });
        }
        reader.readAsDataURL(this.files[0]);
    });

    function updateFrontSlider() {
        $uploadCrop.croppie('result', {
            type: 'canvas',
            size: 'original'
            // size: 'viewport'
        }).then(function (resp) {
            let form = $('#editFrontSliderForm');
            let id = $('#slider_id').val();

            let url = '{{ route('superadmin.front-slider.update', ':id')}}';
            url = url.replace(':id', id);
            $.easyAjax({
                url: url,
                container: '#editFrontSliderForm',
                type: "POST",
                file: true,
                redirect: true,
                data: {"images":resp, "data":form.serialize()},
                success: function (response) {
                    if(response.status == 'success'){
                        $('#application-lg-modal').modal('hide');
                        sliderTable._fnDraw();
                        location.reload();
                    }
                }
            });
        });
    }


    $('body').on('click', '#content_yes', function() {
        $('.slider_content_form').removeClass('d-none');
        $('.slider_contents').removeClass('d-none');
    });

    $('body').on('click', '#content_no', function() {
        $('.slider_content_form').addClass('d-none');
        $('.customData').addClass('d-none');
        $('.slider_contents').addClass('d-none');
    });

    $('body').on('click', '#custom_no', function() {
        $('.customData').removeClass('d-none');
    });

    $('body').on('click', '#custom_yes', function() {
        $('.customData').addClass('d-none');
    });

    $('.dropify').dropify({
        messages: {
            default: '@lang("app.dragDrop")',
            replace: '@lang("app.dragDropReplace")',
            remove: '@lang("app.remove")',
            error: '@lang("errors.invalidFile")'
        }
    });

    $(function () {
        $('#slider_content').summernote({
            dialogsInBody: true,
            height: 200,
            toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen"]]
        ]
        })
    });

</script>
