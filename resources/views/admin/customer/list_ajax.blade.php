@forelse($customers as $customer)
    <div class="col-md-3">
        <!-- Widget: user widget style 1 -->
        <div class="card card-widget widget-user customer-card" data-customer-id="{{ $customer->id }}">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header text-white" style="background-color: var(--active-color)">
                <h5 class="widget-user-username">{{ ucwords($customer->name) }}</h5>
                <h6 class="widget-user-desc"><i class="fa fa-envelope"></i> {{ $customer->email ?? '--' }}</h6>
                <h6 class="widget-user-desc"><i class="fa fa-phone"></i> {{ !is_null($customer->formatted_mobile) ? $customer->formatted_mobile : '--' }}</h6>
            </div>
            <div class="widget-user-image">
                <img class="img-circle elevation-2" src="{{ !is_null($customer->user_image_url) ? $customer->user_image_url : asset('img/default-avatar-user.png') }}" alt="User Avatar">
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-6 border-right">
                        <div class="description-block">
                            <h5 class="description-header">{{ count($customer->completedBookings) }}</h5>
                            <span class="description-text">@lang('menu.bookings')</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-6">
                        <div class="description-block">
                            <h5 class="description-header">{{  \Carbon\Carbon::parse($customer->customerBookings()->first()->created_at)->format($settings->date_format) }}</h5>

                            <span class="description-text">@lang('modules.customer.since')</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
        </div>
        <!-- /.widget-user -->
    </div>
@empty
    <div class="col-md-4">
        @lang('messages.noRecordFound')
    </div>
@endforelse

@php
    $loadedRecords = ($totalRecords - ($totalRecords - count($customers)));
    $takeRecords = $recordsLoad + $loadedRecords;
@endphp

@if($totalRecords > $loadedRecords)
    <div class="col-md-12 text-center">
        <a href="javascript:;" data-take="{{ $takeRecords }}" id="load-more" class="btn btn-lg btn-outline-dark">@lang('app.loadMore')</a>
    </div>
@endif


<script>
    $('body').on('click', '.customer-card', function() {
        let customerId = $(this).data('customer-id');

        var url = "{{ route('admin.customers.show',':id') }} ";
        url = url.replace(':id', customerId);

        window.location.href = url;
    });
</script>
