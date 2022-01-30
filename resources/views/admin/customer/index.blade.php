
@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-light">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input class="form-control form-control-lg" type="text" id="customer-search" placeholder="@lang('modules.customer.search')">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row" id="customer-list">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-js')
    <script>
        $(document).ready(function() {

            const showCustomerList = (take = {{ $recordsLoad }}) => {
                let param = $('#customer-search').val();

                $.easyAjax({
                    type: 'GET',
                    url: '{{ route('admin.customers.index') }}',
                    data: {'param': param, 'take': take},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            $('#customer-list').html(response.view);
                        }
                    }
                });
            };

            $('#customer-search').keyup(function () {
                showCustomerList();
            });

            $('body').on('click', '#load-more', function () {
                let take = $(this).data('take');
                showCustomerList(take);
            });

            showCustomerList();
        });
    </script>
@endpush
