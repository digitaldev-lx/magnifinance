<style>
    .deal-detail-table {
        background: rgba(211, 224, 252, 0.309804);
    }
    .deal-detail-img-thumb img {
        object-fit: cover;
        height: 252px;
    }
    .day_div {
        margin-left: 20px;
    }
</style>

<div class="modal-header">
    <h4 class="modal-title">@lang('menu.deal') @lang('app.detail')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="row">
                <div class="col-md-6 deal-detail-img-thumb">
                    <img src="{{$deal->deal_image_url}}" class="img img-responsive img-thumbnail" width="100%">
                </div>

                <div class="col-md-6">

                    <div class="row">
                        <div class="col-md-6">
                            <h6>@lang('app.title')</h6>
                            <p>{{ $deal->title }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 >@lang('app.discount') @lang('app.type')</h6>
                            <p> {{ $deal->discount_type }} </p>
                        </div>

                        @if ($deal->discount_type=='percentage')
                            <div class="col-md-6">
                                <h6>@lang('app.percentage')</h6>
                                <p> {{ $deal->percentage }}% </p>
                            </div>
                        @else
                            <div class="col-md-6">
                                <h6>@lang('app.amount')</h6>
                                <p> {{ $deal->original_amount-$deal->deal_amount }} </p>
                            </div>
                        @endif


                        <div class="col-md-6">
                            <h6>@lang('app.StartTime')</h6>
                            <p>{{ \Carbon\Carbon::parse($deal->start_date_time)->translatedFormat($settings->date_format.' '.$settings->time_format) }}</p>
                        </div>

                        <div class="col-md-6">
                            <h6>@lang('app.endTime')</h6>
                            <p>{{ \Carbon\Carbon::parse($deal->end_date_time)->translatedFormat($settings->date_format.' '.$settings->time_format) }}</p>
                        </div>

                        <div class="col-md-6">
                            <h6>@lang('app.appliedBeweenTime')</h6>
                            <p>{{ \Carbon\Carbon::parse($deal->open_time)->translatedFormat($settings->time_format) }} - {{ \Carbon\Carbon::parse($deal->close_time)->translatedFormat($settings->time_format) }}</p>
                        </div>

                        <div class="col-md-6">
                            <h6>@lang('app.usesTime')</h6>
                            <p>
                                @if($deal->uses_limit > 0)
                                {{ $deal->uses_limit }}
                                @else
                                    @lang('app.infinite')
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h6>@lang('app.dealUsedTime')</h6>
                            <p>
                                @if($deal->used_time !='')
                                {{ $deal->used_time }}
                                @else
                                    0
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h6 >@lang('app.dayForApply')</h6>
                            <p>
                                @if(sizeof($days) == 7)
                                    @lang('app.allDays')
                                @else
                                    @forelse($days as $day)
                                        <span class="day_div"> @lang('app.'. strtolower($day)) </span>
                                    @empty
                                    @endforelse
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h6 >@lang('app.tax')</h6>
                            @foreach ($selectedTax as $item)
                                <span>{{ $item->name }}-{{ $item->percent }}% @if(!$loop->last),@endif </span>
                            @endforeach
                        </div>



                    </div>
                </div>

                @if(!is_null($deal->description))
                    <div class="col-md-12">
                        <h6 class="text-uppercase">@lang('app.description')</h6>
                        <p>{!! $deal->description !!} </p>
                    </div>
                @endif

                <div class="col-md-12">
                    <h5 class="mb-4">@lang('app.dealItem')</h5>
                    <div class="table table-responsive" id="result_div">
                        <table class="table table-bordered table-condensed" width="100%">
                            <tr class="deal-detail-table">
                                <th>@lang('app.service')</th>
                                <th>@lang('app.unitPrice')</th>
                                <th>@lang('app.quantity')</th>
                                <th>@lang('app.subTotal')</th>
                                <th>@lang('app.discount')</th>
                                <th>@lang('app.total')</th>
                            </tr>
                            @foreach ($deal_items as $deal_item)
                                <tr>
                                    <td>{{$deal_item->businessService->name}}</td>
                                    <td>{{currencyFormatter($deal_item->unit_price,myCurrencySymbol())}}</td>
                                    <td>{{$deal_item->quantity}}</td>
                                    <td>{{currencyFormatter($deal_item->quantity*$deal_item->unit_price,myCurrencySymbol())}}</td>
                                    <td>{{currencyFormatter($deal_item->discount_amount,myCurrencySymbol())}}</td>
                                    <td>{{currencyFormatter($deal_item->total_amount,myCurrencySymbol())}}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="3"></td>
                                <td id="deal-sub-total">{{ currencyFormatter($deal->original_amount,myCurrencySymbol())}}</td>
                                <td id="deal-discount-total">{{currencyFormatter($deal->original_amount-$deal->deal_amount,myCurrencySymbol())}}</td>
                                <td id="deal-total-price">{{ currencyFormatter($deal->deal_amount,myCurrencySymbol())}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
</div>


