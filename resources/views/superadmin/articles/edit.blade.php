@extends('layouts.master')

@push('head-css')
    <link rel="stylesheet" href="{{ asset('css/bootstrap-tagsinput.css') }}">
    <style>
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #999;
        }
        .select2-dropdown .select2-search__field:focus, .select2-search--inline .select2-search__field:focus {
            border: 0px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            margin: 0 13px;
        }
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #cfd1da;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__clear {
            cursor: pointer;
            float: right;
            font-weight: bold;
            margin-top: 8px;
            margin-right: 15px;
        }
        .select2 {
            width: 100%;
        }
        .bootstrap-tagsinput {
            width: 100%;
        }
        .bootstrap-tagsinput .tag {
            margin-right: 2px;
            padding: 2px 5px;
            border-radius: 2px;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.edit') @lang('menu.article')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="createForm" class="ajax-form" method="POST">
                        @csrf
                        <input type="hidden" name="redirect_url" value="{{ url()->previous() }}">

                        <div class="row">
                            <div class="col-md">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.article') @lang('app.title')</label>
                                    <input type="text" name="title" id="title" class="form-control form-control-lg" value="{{ $article->title }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label>@lang('app.article') @lang('app.slug')</label>
                                    <input type="text" name="slug" id="slug" class="form-control form-control-lg" value="{{ $article->slug }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('app.category')</label>
                                    <div class="input-group">
                                        <select name="category_id" id="category_id" class="form-control form-control-lg">
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{$article->category_id == $category->id ? 'checked': ''}}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('app.article') @lang('app.excerpt') <small>(250 {{__('app.characters')}})</small></label>
                                    <textarea name="excerpt" id="excerpt" cols="30" class="form-control-lg form-control" rows="4">{{ !empty($article) ? $article->excerpt : '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('app.article') @lang('app.content')</label>
                                    <textarea name="content" id="content" cols="30" class="form-control-lg form-control" rows="4">{{ !empty($article) ? $article->content : '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">@lang('app.image')</label>
                                    <div class="card">
                                        <div class="card-body">
                                            <input type="file" id="input-file-now" name="image" accept=".png,.jpg,.jpeg" data-default-file="{{ asset($article->article_image_url)  }}" class="dropify"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">@lang('app.article')
                                        @lang('app.keywords')</label>
                                    <input type="text" class="form-control form-control-lg" value="{{ !empty($article) ? $article->keywords : '' }}"
                                           id="keywords" name="keywords" data-role="tagsinput"/>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">@lang('app.article')
                                        @lang('app.description')</label>
                                    <textarea name="seo_description" id="seo_description" cols="30"
                                              class="form-control-lg form-control" rows="3">{{ !empty($article) ? $article->seo_description : '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('app.status')</label>
                                    <div class="input-group">
                                        <select name="status" id="status" class="form-control form-control-lg">
                                            @php($statuses = ['saved', 'pending', 'approved'])
                                            @foreach($statuses as $status)
                                                <option value="{{ $status }}" {{$article->status == $status ? 'checked': ''}}>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">

                                <div class="form-group">
                                    <button type="button" id="submit-form" class="btn btn-success btn-light-round"><i
                                                class="fa fa-save"></i> @lang('app.save')</button>

                                </div>

                            </div>
                        </div>

                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
@endsection

@push('footer-js')
    <script src="{{ asset('js/bootstrap-tagsinput.js') }}"></script>

    <script>
        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

        $('#content').summernote({
            dialogsInBody: true,
            height: 300,
            /*toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['fontsize', ['fontsize']],
                ['para', ['ul', 'ol', 'paragraph']],
                ["view", ["fullscreen"]]
            ]*/
        })

        const createSlug = function (str) {
            str = str.replace(/^\s+|\s+$/g, ''); // trim
            str = str.toLowerCase();

            // remove accents, swap ñ for n, etc
            let from = "ÁÄÂÀÃÅČÇĆĎÉĚËÈÊẼĔȆÍÌÎÏŇÑÓÖÒÔÕØŘŔŠŤÚŮÜÙÛÝŸŽáäâàãåčçćďéěëèêẽĕȇíìîïňñóöòôõøðřŕšťúůüùûýÿžþÞĐđßÆa·/_,:;";
            let to = "AAAAAACCCDEEEEEEEEIIIINNOOOOOORRSTUUUUUYYZaaaaaacccdeeeeeeeeiiiinnooooooorrstuuuuuyyzbBDdBAa------";
            for (let i = 0, l = from.length; i < l; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }

            str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-'); // collapse dashes

            $('#slug').val(str);
        };

        $('#title').keyup(function(e) {
            createSlug($(this).val());
        });

        $('#slug').keyup(function(e) {
            createSlug($(this).val());
        });

        function submitForm(){
            $.easyAjax({
                url: '{{route('superadmin.articles.update', $article->id)}}',
                container: '#createForm',
                type: "POST",
                file:true,
                formReset:false,
                data: {data: $('#createForm').serialize()},
                success: function (response){
                    console.log(response);
                },
                error: function (error){
                    if( error.status === 422 ) {
                        var data = error.responseJSON.errors
                    }
                    $.each( data, function( key, value ) {
                        $.showToastr(value[0], 'error');
                    });
                }
            })
        }

        $('body').on('click', '#submit-form', function() {
            submitForm()
        });

    </script>
@endpush
