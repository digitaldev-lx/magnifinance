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
                        {{--@if (in_array('Employee Leave',$user->modules) && $user->roles()->withoutGlobalScopes()->first()->hasPermission('read_employee_leave'))
                        <a href="{{ route('admin.employee-leaves.index') }}" class="btn btn-rounded mb-1 mr-2" id="employee-leaves"><i class="fa fa-rocket"></i> @lang('app.employee') @lang('app.leave')</a>
                        @endif
                        @permission('read_employee_group')
                        <a href="{{ route('admin.employee-group.index') }}" class="btn btn-rounded btn-info mb-1 mr-2"><i class="fa fa-list"></i> @lang('app.employeeGroup')</a>
                        @endpermission--}}
{{--                        @permission('create_tout')--}}
{{--                        <a href="{{ route('admin.toutes.create') }}" class="btn btn-rounded btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>--}}
{{--                        @endpermission--}}
                    </div>
                    <div class="table-responsive">
                        <table id="myTable" class="table w-100">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{__('app.company')}}</th>
                                <th width="15%">{{__('app.period')}}</th>
                                <th>{{__('app.tout_local')}}</th>
                                <th width="20%">{{__('app.tout_in')}}</th>
                                <th>{{__('app.amount')}}</th>
                                <th>{{__('app.created_at')}}</th>
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
                ajax: '{!! route('superadmin.toutes.index') !!}',
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [
                    { data: 'DT_RowIndex'},
                    { data: 'company', name: 'company' },
                    { data: 'period', name: 'period' },
                    { data: 'tout_local', name: 'tout_local' },
                    { data: 'tout_in', name: 'tout_in' },
                    { data: 'amount', name: 'amount' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action' }
                ]
            });
            new $.fn.dataTable.FixedHeader( table );

        });
    </script>
@endpush
