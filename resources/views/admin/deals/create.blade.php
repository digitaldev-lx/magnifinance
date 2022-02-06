@extends('layouts.master')

@push('head-css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">
    <style>
        .collapse.in{
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
        input::-webkit-inner-spin-button
        {
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
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.add') @lang('menu.deal')</h3>
                </div>
                <div class="card-body">
                    <form role="form" id="createForm" class="ajax-form" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.title') </label>
                                    <input type="text" class="form-control" name="title" id="title" value="{{ !empty($deal) ? $deal->title:'' }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.slug') </label>
                                    <input type="text" class="form-control" name="slug" id="slug" value="{{ !empty($deal) ? $deal->slug.'-1':'' }}"autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>@lang('app.applyDealOn')</label>
                                    <select name="choice" id="choice" class="form-control form-control-lg select2">
                                        <option value="service">@lang('app.service')</option>
                                        <option value="location">@lang('app.location')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="service_div">
                                <div class="form-group">
                                    <label>@lang('app.service')</label>
                                    <select name="services[]" id="services" class="form-control form-control-lg select2" multiple="multiple">
                                        <option value="">@lang('app.selectServices')</option>
                                        @foreach($services as $service)
                                            <option {{ !empty($deal_services) && in_array($service->id, $deal_services) ? 'selected' :'' }} value="{{ $service->id }}">{{ $service->name.' ('.$service->location->name.')' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="location_div">
                                <div class="form-group">
                                    <label>@lang('app.location')</label>
                                    <select name="locations" id="locations" class="form-control form-control-lg select2">
                                        <option value="">@lang('app.selectLocation')</option>
                                        @foreach($locations as $location)
                                            <option {{ !empty($deal) && $deal->location->id == $location->id ? 'selected' :'' }} value="{{ $location->id }}">{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="make_deal_div">
                                <button type="button" class="btn btn-default" id="make_deal">
                                    <i class="fa fa-plus"></i>  @lang('app.makeDeal')
                                </button>
                                <button id="reset-btn" type="button" class="btn btn-default">
                                    <i class="fa fa-refresh"></i>  @lang('app.reset')
                                </button>
                            </div>
                            <div class="offset-md-1 col-md-10 offset-md-1">
                                <div class="table table-responsive" id="result_div">
                                    @if (!empty($deal_items_table))
                                    {!! $deal_items_table !!}
                                    @endif
                                </div>
                            </div>
                            <div class="row deal-form {{ !empty($deal) ? '' :'d-none' }}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">@lang('app.discount') @lang('app.type')</label>
                                    <select name="discount_type" id="discount_type" class="form-control">
                                        <option @if (!empty($deal) && $deal->discount_type=='percentage') selected @endif value="percentage"> @lang('app.percentage') </option>
                                        <option @if (!empty($deal) && $deal->discount_type!='percentage') selected @endif value="{{ $settings->currency->currency_name }}"> {{ $settings->currency->currency_name }} </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.discount') @lang('app.percentage')</label>
                                    <input onkeypress="return isNumberKey(event)" type="number" class="form-control checkAmount" name="discount" id="discount" value="{{ !empty($deal) ? $deal->percentage:'0' }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.dealPrice')</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">{{$settings->currency->currency_symbol}}</span>
                                        </div>
                                        <input onkeypress="return isNumberKey(event)" readonly type="number" class="form-control checkAmount" name="discount_amount" id="discount_amount" value="{{ !empty($deal) ? $deal->deal_amount:'0' }}" min="0">
                                        <input type="hidden" class="form-control" name="original_amt" id="original_amt" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.dealLimit')</label>
                                    <input type="number" class="form-control" name="uses_time" min="0"  value="{{ !empty($deal) ? $deal->uses_limit:'' }}" onkeypress="return isNumberKey(event)">
                                    <span class="help-block">@lang('messages.dealLimit')</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.customerUseLimit')</label>
                                    <input onkeypress="return isNumberKey(event)" type="number" class="form-control" name="customer_uses_time" value="{{ !empty($deal) ? $deal->max_order_per_customer:'1' }}" min="0">
                                    <span class="help-block">@lang('messages.howManyTimeCustomerCanUse')</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">@lang('app.status')</label>
                                    <select name="status" class="form-control">
                                        <option value="active"> @lang('app.active') </option>
                                        <option value="inactive"> @lang('app.inactive') </option>
                                    </select>
                                </div>
                            </div>

                            @if (!is_null($taxes))
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>@lang('app.select') @lang('app.tax')</label>
                                        <select name="tax_ids[]" id="tax_ids" class="form-control form-control-lg" style="width: 100% !important;">
                                            <option value="">@lang('app.select') @lang('app.tax')</option>
                                            @foreach($taxes as $tax)
                                                <option value="{{ $tax->id }}">{{ $tax->name }} {{ $tax->percent }}%</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.appliedBetweenDateTime')</label>
                                    <input type="text" class="form-control" id="daterange" name="applied_between_dates" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('modules.settings.openTime')</label>
                                    <input type="text" class="form-control" id="open_time" name="open_time" autocomplete="off" value="{{ !empty($deal) ? $deal->open_time:'' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('modules.settings.closeTime')</label>
                                    <input type="text" class="form-control" id="close_time"  name="close_time" autocomplete="off" value="{{ !empty($deal) ? $deal->close_time:'' }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label>@lang('app.dayForApply') </label>
                                <div class="row">
                                    @forelse($days as $day)
                                        <div class="form-group" id="day-div">
                                            <label>
                                                <div class="icheckbox_flat-green" aria-checked="false" aria-disabled="false" id="icheckbox_flat">
                                                    <input type="checkbox" {{!empty($selectedDays)?(in_array($day, $selectedDays)?'checked':''):'checked'}}  value="{{$day}}" name="days[]" class="flat-red columnCheck"  id= "columnCheck">
                                                    <ins class="iCheck-helper" id="iCheck-helper"></ins>
                                                </div>
                                                @lang('app.'. strtolower($day))
                                            </label>
                                        </div>
                                    @empty
                                    @endforelse
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">@lang('app.description')</label>
                                    <textarea name="description" id="description" cols="30" class="form-control-lg form-control" rows="4">{{ !empty($deal) ? $deal->description:'' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">@lang('app.image')</label>
                                    <div class="card">
                                        <div class="card-body">
                                            <input type="file" id="input-file-now" name="feature_image" accept=".png,.jpg,.jpeg" data-default-file="{{ asset('img/no-image.jpg')  }}" class="dropify"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="button" id="save-form" class="btn btn-success btn-light-round">
                                        <i class="fa fa-check"></i> @lang('app.save')
                                    </button>
                                </div>
                            </div>
                          </div>
                        </div>
                        <input type="hidden" name="deal_startDate" id="deal_startDate">
                        <input type="hidden" name="deal_endDate" id="deal_endDate">
                        <input type="hidden" name="deal_startTime" id="deal_startTime" value="{{ !empty($deal) ? \Carbon\Carbon::parse($deal->open_time)->format('h:i A') :''}}">
                        <input type="hidden" name="deal_endTime" id="deal_endTime" value="{{ !empty($deal) ? \Carbon\Carbon::parse($deal->close_time)->format('h:i A') :''}}">
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
@endsection

@push('footer-js')
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>

    <script>

        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

        $('#open_time').datetimepicker({
            format: '{{ $time_picker_format }}',
            locale: '{{ $settings->locale }}',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-angle-double-left",
                next: "fa fa-angle-double-right",
            },
            useCurrent: false,
        }).on('dp.change', function(e) {
            $('#deal_startTime').val(convert(e.date));
        });

        $('#close_time').datetimepicker({
            format: '{{ $time_picker_format }}',
            locale: '{{ $settings->locale }}',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-angle-double-left",
                next: "fa fa-angle-double-right",
            },
            useCurrent: false,
        }).on('dp.change', function(e) {
            $('#deal_endTime').val(convert(e.date));
        });

        $(function () {
            $('#description').summernote({
                dialogsInBody: true,
                height: 300,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['fontsize', ['fontsize']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ["view", ["fullscreen"]]
                ]
            });
        });

        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
        });

        $(document).on('keyup', '.checkAmount', function () {
            var original_amount = $('#original_amount').val();
            var discount_amount = $('#discount_amount').val();
            if(original_amount!='' && discount_amount!='' && Number(discount_amount) > Number(original_amount)){
                $('#discount_amount').focus();
                $('#discount_amount').val('');
            }
        });

        $(function() {
            moment.locale('{{ $settings->locale }}');
            $('input[name="applied_between_dates"]').daterangepicker({
                timePicker: true,
                minDate: moment().startOf('hour'),
                autoUpdateInput: false,
            });
        });

        $('input[name="applied_between_dates"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('{{ $date_picker_format }} {{$time_picker_format}}') + '--' + picker.endDate.format('{{ $date_picker_format }} {{$time_picker_format}}'));
            $('#deal_startDate').val(picker.startDate.format('YYYY-MM-DD')+' '+convert(picker.startDate));
            $('#deal_endDate').val(picker.endDate.format('YYYY-MM-DD')+' '+convert(picker.endDate));
        });

        $('body').on('change', '#services', function() {
            var services = $(this).val();
            if($('#choice').val()=='location'){
                $('#result_div').html('');
                $('.deal-form').addClass('d-none');
                return false;
            }
            $.ajax({
                url: "{{route('admin.deals.selectLocation')}}",
                type: "POST",
                container: '#createForm',
                data:$('#createForm').serialize(),
                success:function(data){
                    $('#locations').html(data.selected_location);
                    $('#locations').val(data.selected_location);
                }
            })
        });

        $('body').on('change', '#locations', function() {
            var locations = $(this).val();
            if($('#choice').val()=='service'){
                $('#result_div').html('');
                $('.deal-form').addClass('d-none');
                return false;
            }
            $.ajax({
                url: "{{route('admin.deals.selectServices')}}",
                container: '#createForm',
                type: "POST",
                data: $('#createForm').serialize(),
                success:function(data){
                    $('#services').html(data.selected_service);
                    $('#services').val(data.selected_service);
                }
            })
        });

        $(document).on('change', '#services', function(){
            $('#result_div').html('');
            $('.deal-form').addClass('d-none');
        });

        $(document).on('change', '#locations', function(){
            $('#result_div').html('');
            $('.deal-form').addClass('d-none');
        });

        $('body').on('click', '#reset-btn', function() {
            $.ajax({
                url: "{{route('admin.deals.resetSelection')}}",
                type: "GET",
                success:function(data){
                    $('#locations').html(data.all_locations_array);
                    $('#services').html(data.all_services_array);
                    $('#result_div').html('');
                    $('#discount_amount').val(0);
                    $('.deal-form').addClass('d-none');
                }
            })
        });

        $('body').on('click', '#make_deal', function() {
            var locations = $('#locations').val();
            var services = $('#services').val();
            var choice = $('#choice').val();
            if(services.length==0){
                toastr.error('@lang("messages.deal.selectOneService")');
                return false;
            }
            else if(!locations || locations.length==0){
                toastr.error('@lang("messages.deal.selectOneLocation")');
                return false;
            }
            $.ajax({
                url: "{{route('admin.deals.makeDeal')}}",
                type: "POST",
                container: '#createForm',
                data:$('#createForm').serialize(),
                success:function(data){
                    $('#result_div').html(data.view);
                    $('.deal-form').removeClass('d-none');

                }
            });
        });

        $('body').on('change', '#choice', function() {
            var location_div = $('#location_div').html(), service_div = $('#service_div').html(), choice = $(this).val();
            if(choice=='service'){
                $("#location_div").before($("#service_div"));
            }
            else if(choice=='location'){
                $("#service_div").before($("#location_div"));
            }
            $('#reset-btn').click();
        });

        $(document).on('click', '.quantity-minus', function () {
            let serviceId = $(this).data('service-id');
            let qty = $('.deal-service-'+serviceId).val();
            qty = parseInt(qty)-1;
            if(qty < 1){
                return false;
            }
            $('.deal-service-'+serviceId).val(qty);
            updateCartQuantity(serviceId, qty);
        });

        $(document).on('click', '.quantity-plus', function () {
            let serviceId = $(this).data('service-id');
            let qty = $('.deal-service-'+serviceId).val();
            qty = parseInt(qty)+1;
            $('.deal-service-'+serviceId).val(qty);
            updateCartQuantity(serviceId, qty);
        });

        $(document).on('keyup', '.deal_discount', function () {
            let discount = $(this).val();
            if(discount=='' && discount>=0){
                $(this).val(0);
            }
            calculateTotal();
        });

        $(document).on('change', '#discount_type', function () {
            let discount_type = $('#discount_type').val();
            if(discount_type=='percentage') {
                $('#discount').prop("readonly", false);
            }
            else {
                $('#discount').prop("readonly", true);
                $('#discount').val(0);
            }
            $('.deal_discount').val(0);
            calculateTotal();
        });

        $(document).on('keyup', '#discount', function () {
            let percent_discount = $(this).val();
            if(percent_discount>100){
                $(this).val(0);
            }
            calculateTotal();
        });

        $('body').on('click', '.delete-row', function() {
            let id = $(this).data('deal-id');
            let name = $(this).data('deal-name');

            $("#row"+id).remove();
            $("#services option[value='"+name+"']").detach();
            if ($('#services option:selected').length == 0) {
                hideDealForm()
            }
            calculateTotal();
        });

        function updateCartQuantity(serviceId, qty) {
            let servicePrice = $('.deal-price-'+serviceId).val();
            let subTotal = (parseFloat(servicePrice) * parseInt(qty));
            $('.deal-subtotal-'+serviceId).html(currency_format(subTotal.toFixed(2)));
            $('.deal-subtotal-val-'+serviceId).val(subTotal.toFixed(2));
            calculateTotal();
        }

        function calculateTotal() {
            let dealSubTotal = 0;
            let discount = 0;
            let dealTotal = 0;
            let total_discount=0;
            let discountPercentage = 0;
            let discount_type = $('#discount_type').val();

            if($('#discount').val()!='' || $('#discount').val()>0){
                discountPercentage = $('#discount').val();
            }

            $("input[name='deal_discount[]']").each(function(index){
                let discount_price = $(this).val();
                let sub_total_price = $("input[name='deal-subtotal-val[]']").eq(index).val();

                if(discount_type=='percentage'){
                    $(this).prop("readonly", true);
                    discount_price = sub_total_price * (discountPercentage/100);
                    $(this).val(discount_price.toFixed(2));
                }
                else{
                    $(this).prop("readonly", false);
                    if(discount_price>0){ /* for NAN validation */
                        if(parseFloat(discount_price)>sub_total_price){
                            dealTotal=0;
                            $(this).val(0);
                            discount_price = 0;
                        }
                    }
                }
                total_discount = parseFloat(total_discount) + parseFloat(discount_price);
            });

            $("input[name='deal_unit_price[]']").each(function(index)
            {
                let servicePrice = $(this).val();
                let qty = $("input[name='deal_quantity[]']").eq(index).val();
                let sub_discount = $("input[name='deal_discount[]']").eq(index).val();
                let sub_total = parseFloat(servicePrice) * parseInt(qty);
                discount = parseFloat(sub_total) - parseFloat(sub_discount);

                if(parseFloat(sub_discount)>sub_total){
                    discount = sub_total;
                }
                if(sub_discount>0){
                    $("td[name='deal-total[]']").eq(index).html(currency_format(discount.toFixed(2)));
                }
                else{
                    $("td[name='deal-total[]']").eq(index).html(currency_format(sub_total.toFixed(2)));
                }
                dealSubTotal = (dealSubTotal + sub_total);
            });

            let deal_total_price=0;
            if(total_discount<dealSubTotal){
                deal_total_price = dealSubTotal-total_discount;
            }

            $("#deal-sub-total").html(currency_format(dealSubTotal.toFixed(2)));
            $("#deal-discount-total").html(currency_format(total_discount.toFixed(2)));
            $("#deal-total-price").html(currency_format( deal_total_price.toFixed(2)));
            $('#discount_amount').val(deal_total_price.toFixed(2));
            $('#original_amt').val(dealSubTotal.toFixed(2));
        }

        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
            return true;
        }

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

        $(document).on('keyup', '#title', function () {
            createSlug($(this).val());
        });

        $(document).on('keyup', '#slug', function () {
            createSlug($(this).val());
        });

        $('body').on('click', '#save-form', function() {
            $.easyAjax({
                url: '{{route('admin.deals.store')}}',
                container: '#createForm',
                type: "POST",
                redirect: true,
                file:true,
            })
        });

        function convert(str) {
            var date = new Date(str);
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0'+minutes : minutes;
            hours = ("0" + hours).slice(-2);
            var strTime = hours + ':' + minutes + ' ' + ampm;
            return strTime;
        }
    </script>
    @include("partials.currency_format")
@endpush
