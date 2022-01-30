@extends('layouts.master')

@push('head-css')
    <link href=" {{ asset('front/css/bootstrap-datepicker.css') }} " rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/booking-step-4.css') }}">
    <style>
        .collapse.in {
            display: block;
        }

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

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        .select2 {
            width: 100%;
        }

        textarea {
            min-height: 173px;
        }

        #d-none {
            display: none;
        }

        #day-div {
            margin-left: 1em
        }

        #icheckbox_flat {
            position: relative;
            margin-right: 5px;
        }

        #columnCheck {
            position: absolute;
            opacity: 0;
            margin-left: 15px;
        }

        #iCheck-helper {
            position: absolute;
            top: 0%;
            left: 0%;
            display: block;
            width: 100%;
            height: 100%;
            margin: 0px;
            padding: 0px;
            background: rgb(255, 255, 255);
            border: 0px;
            opacity: 0;
        }

        #make_deal_div {
            margin-top: 2em;
        }

        @media (max-width: 1023px) {
            .select2 {
                max-width: 355px;
            }
        }
    </style>
@endpush

@section('content')
        <div class="row">
            <div class="col-md-12">
                <div class="card card-dark">
                    <div class="card-header">
                        <h3 class="card-title">@lang('app.add') @lang('menu.advertises')</h3>
                    </div>
                    <div class="card-body">
                        <form role="form" id="editForm" class="ajax-form" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6" id="ads_in_all_category_div">
                                            <div class="form-group">
                                                <label>{{__('app.ads_in_all_category')}}</label>
                                                <select name="ads_in_all_category" id="ads_in_all_category"
                                                        class="form-control form-control-md ">
                                                    <option {{$advertise->ads_in_all_category == 'yes' ? 'selected' : ''}} value="yes">{{__('app.yes')}}</option>
                                                    <option {{$advertise->ads_in_all_category == 'no' ? 'selected' : ''}} value="no">{{__('app.no')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" id="category_div">
                                                <label id="label">@lang('app.category')</label>
                                                <select name="category_id" id="category_id"
                                                        class="form-control form-control-md select2">
                                                    <option value="">{{__('app.selectCategory')}}</option>
                                                    @foreach($categories as $category)
                                                            <option {{$category->id == $advertise->category_id ? 'selected' : ''}} value="{{ $category->id }}">{{ $category->name }}</option>

                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group" id="article_div">
                                                <label id="label">{{__('app.advertiseInArticle')}}</label>
                                                <select name="article_id" id="article_id"
                                                        class="form-control form-control-md select2">
                                                    <option value="">{{__('app.selectArticle')}}</option>
                                                    @foreach($articles as $article)
                                                            <option {{$article->id == $advertise->article_id ? 'selected' : ''}} value="{{ $article->id }}">{{ $article->title }}</option>

                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('app.title') </label>
                                                <input type="text" class="form-control" name="title" id="title" value="{{$advertise->title}}"
                                                       autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('app.showToPeopleInLocation') }}</label>
                                                <div class="input-group">
                                                    <select name="location_id" id="location_id" class="form-control form-control-md">
                                                        <option value="">{{ __('front.allLocations') }}</option>
                                                        @foreach($locations as $location)
                                                            <option {{$location->id == $advertise->location_id ? 'selected' : ''}} value="{{ $location->id }}">{{ $location->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>{{__('Info 1')}}</label>
                                                <input type="text" class="form-control" name="info1" id="info1" value="{{$advertise->info1}}"
                                                       autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>{{__('Info 2')}}</label>
                                                <input type="text" class="form-control" name="info2" id="info2" value="{{$advertise->info2}}"
                                                       autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>{{__('Info 3')}}</label>
                                                <input type="text" class="form-control" name="info3" id="info3" value="{{$advertise->info3}}"
                                                       autocomplete="off">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('app.description') <small>(Min 150 - Max 200)</small> - <span class="text-bold" id="charNum">0</span> {{__('app.characters')}}</label>
                                                <textarea type="text" class="form-control" name="description" onkeyup="countChar(this)"
                                                          id="description">{{$advertise->description}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{__('Call To Action')}}</label>
                                                <input type="text" class="form-control" name="call_to_action" id="call_to_action" value="{{$advertise->call_to_action}}"
                                                       autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{__('Link')}} <span class="font-weight-bold red invalid-link"></span>
                                                </label>
                                                <input type="text" class="form-control" name="link" id="link" value="{{$advertise->link}}"
                                                       autocomplete="off">

                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{__('app.price')}}</label>
                                                <input type="number" class="form-control" placeholder="0.00" required name="price" min="0" value="{{$advertise->price}}" step="0.01" title="Currency" pattern="^\d+(?:\.\d{1,2})?$"
                                                       onblur="this.parentNode.parentNode.style.backgroundColor=/^\d+(?:\.\d{1,2})?$/.test(this.value)?'inherit':'red'" />
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{__('app.status')}}</label>
                                                <select name="status" id="status"
                                                        class="form-control form-control-md ">
                                                    <option {{$advertise->status == 'completed' ? 'selected' : ''}} value="Completed">{{__('app.completed')}}</option>
                                                    <option {{$advertise->status == 'pending' ? 'selected' : ''}} value="Pending">{{__('app.pending')}}</option>
                                                </select>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="image">@lang('app.image')</label>
                                                <div class="card">
                                                    <div class="card-body">
                                                        <input type="file" id="input-file-now" name="image" accept=".png,.jpg,.jpeg" data-default-file="{{ asset($advertise->image)  }}" class="dropify" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-6 bg-gray-light p-3 shadow-2" style="border-radius: 10px">
                                    <div class="row text-center shadow-sm p-2 mb-5 bg-white rounded" style="border-radius: 10px">
                                        <div class="col-md-12">
                                            <h4>{{__('app.advertise')}}</h4>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label id="label">{{__('app.advertise')}} {{__('app.from')}}</label>

                                            <div class="form-group">
                                                <input type="text" class="form-control datepicker" name="from" id="from" value="{{$advertise->from}}"
                                                       placeholder="@lang('app.startDate')" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label id="label"> {{__('app.to')}}</label>

                                            <div class="form-group">
                                                <input type="text" class="form-control datepicker" name="to" id="to" value="{{$advertise->to}}"
                                                       placeholder="@lang('app.endDate')" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{__('app.amount')}}</label>
                                                <input type="text" class="form-control" placeholder="0.00" required readonly name="amount" id="amount" value="{{$advertise->formated_amount_to_pay}}" title="{{__('app.amount')}}" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{__('app.averageDaily')}}</label>
                                                <input type="text" class="form-control" name="avg_amount" readonly id="avg_amount" value="{{$advertise->formated_avg_amount_to_pay}}">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-12 mt-3">
                                    <div class="form-group">
                                        <button type="button" id="save-form" class="btn btn-success btn-light-round">
                                            <i class="fa fa-check mr-2"></i>{{__('app.save')}}
                                        </button>
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
    <script src="{{ asset('front/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        adsDays = 0;
        function validateURL(textval) {
            var regex = /^(https?:\/\/)?[a-z0-9-]*\.?[a-z0-9-]+\.[a-z0-9-]+(\/[^<>]*)?$/;
            return regex.test(textval);
        }

        function countChar(val) {
            var len = val.value.length;
            val.value = val.value.substring(0, 500);
            $('#charNum').text(len);

        }

        function calculateAdsDuration(){
            var from = moment($("#from").val(), 'YYYY-MM-DD')
            var to = moment($("#to").val(), 'YYYY-MM-DD')
            var diff = to.diff(from);
            var amount = $("#amount").val()
            diff = moment(diff).format('D');
            adsDays = diff = parseInt(diff)

            if(diff < 3){
                $.showToastr("{{__('errors.adsAtLeast3Days')}}", 'error');
            }
            if(amount !== 0){
                calculateAvgAmount()
            }
        }

        function calculateAvgAmount(){
            var amount = $("#amount").val()
            var numDays = adsDays
            var avg_amount = amount / numDays
            $("#avg_amount").val(avg_amount)
            console.log(amount, numDays);
        }

        $(document).ready(function(){

            $("#link").bind('blur',
                function ()
                {
                    if(validateURL($(this).val())){
                        $('.invalid-link').html('')
                    }
                    else{
                        $('.invalid-link').html('({{__('errors.invalidLink')}})')
                        $.showToastr("{{__('errors.invalidLink')}}", 'error');
                    }
                }
            )

        });

        $('.datepicker').datepicker({
            templates: {
                leftArrow: '<i class="fa fa-chevron-left"></i>',
                rightArrow: '<i class="fa fa-chevron-right"></i>'
            },
            startDate: '-0d',
            language: '{{ $locale }}',
            weekStart: 0,
            format: "yyyy-mm-dd"
        });

        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });



        $('#ads_in_all_category').change(function () {
            if ($(this).val() == "no") {
                $("#article_div").removeClass('d-none');
                $("#category_div").addClass('d-none');
                $("#article_id").prop('disabled', false);
                $("#category_id").prop('disabled', true);
            } else {
                $("#category_div").removeClass('d-none');
                $("#article_div").addClass('d-none')
                $("#category_id").prop('disabled', false);
                $("#article_id").prop('disabled', true);
            }
        });

        $(document).ready(function () {
            let ads_in_all_category = $('#ads_in_all_category').val()
            if(ads_in_all_category == 'no'){
                $("#category_div").addClass('d-none');
                $("#category_id").prop('disabled', true);
            }else{
                $("#article_div").addClass('d-none');
                $("#article_id").prop('disabled', true);
            }



        })

        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }

        $('body').on('click', '#save-form', function() {
            $.easyAjax({
                url: '{{route('superadmin.advertises.update', $advertise->id)}}',
                headers: { 'X-CSRF-TOKEN': '{{csrf_token()}}' },
                container: '#editForm',
                type: "POST",
                file: true,
                formReset:false,
                data: {data: $('#editForm').serialize()},
                success: function (response){

                },
                error: function (error){
                    console.log(error);
                    if( error.status === 422 ) {
                        var data = error.responseJSON.errors
                    }
                    $.each( data, function( key, value ) {
                        $.showToastr(value[0], 'error');
                    });
                }
            })
        });


    </script>
    @include("partials.currency_format")
@endpush
