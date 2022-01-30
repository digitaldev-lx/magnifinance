<div class="modal-header">
    <h4 class="modal-title">@lang('app.service') @lang('app.detail')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-6 deal-detail-img-thumb">
                <img src="{{ asset( '/user-uploads/service/'.$businessService->id.'/'.$businessService->default_image) }}" class="img img-responsive img-thumbnail" width="100%">
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <h6>@lang('app.service') @lang('app.name')</h6>
                        <p>{{ $businessService->name }}</p>
                    </div>

                    <div class="col-md-6">
                        <h6>@lang('app.discount') @lang('app.type')</h6>
                        <p> {{ $businessService->discount_type }} </p>
                    </div>
                    @if ($businessService->discount_type == 'percent')
                        <div class="col-md-6">
                            <h6>@lang('app.percentage')</h6>
                            <p> {{ $businessService->discount }}% </p>
                        </div>
                    @else
                        <div class="col-md-6">
                            <h6>@lang('app.amount')</h6>
                            <p> {{ $businessService->price - $businessService->discount }} </p>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <h6>@lang('app.location')</h6>
                        <p> {{ $businessService->location->name }} </p>
                    </div>
                    <div class="col-md-6">
                        <h6>@lang('app.category')</h6>
                        <p> {{ $businessService->category->name }} </p>
                    </div>
                    <div class="col-md-6">
                        <h6>@lang('app.tax')</h6>
                        @foreach ($taxes as $item)
                            <span>{{ $item->name }}-{{ $item->percent }}% @if (!$loop->last),@endif </span>
                        @endforeach
                    </div>




                </div>
            </div>
            @if (!is_null($businessService->description))
            <div class="col-md-12">
                <h6>@lang('app.description')</h6>
                <p>{!! $businessService->description !!} </p>
            </div>
        @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
            @lang('app.cancel')</button>
    </div>
