@extends('layouts.master')

@push('head-css')
    <style>
        #employee-leaves {
            background-color: #6666b9;
            color:#ffffff;
        }
        .username-badge{
            margin:0.3em;
            padding:0.3em;
        }

    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-center justify-content-md-end mb-3">

                        @permission('create_advertise')
                        <a href="{{ route('admin.advertises.create') }}" class="btn btn-rounded btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>
                        @endpermission
                    </div>
                    <div class="table-responsive">
                        <table id="myTable" class="table w-100">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('app.image')</th>
                                <th width="15%">{{__('app.period')}}</th>
                                <th>{{__('app.advertise_local')}}</th>
                                <th width="20%">{{__('app.advertise_in')}}</th>
                                <th>{{__('app.amount')}}</th>
                                <th>{{__('app.avgAmount')}}</th>
                                <th>@lang('app.status')</th>
                                <th class="text-right actionth">@lang('app.action')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-js')
    <script>
        $(document).ready(function() {
            var table = $('#myTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('admin.advertises.index') !!}',
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [
                    { data: 'DT_RowIndex'},
                    { data: 'image', name: 'image' },
                    { data: 'period', name: 'period' },
                    { data: 'advertise_local', name: 'advertise_local' },
                    { data: 'advertise_in', name: 'advertise_in' },
                    { data: 'amount', name: 'amount' },
                    { data: 'avg_amount', name: 'avg_amount' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action' }
                ]
            });
            new $.fn.dataTable.FixedHeader( table );

        });
    </script>
@endpush
