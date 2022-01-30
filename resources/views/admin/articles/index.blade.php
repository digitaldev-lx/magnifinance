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
                        @permission('create_article')
                        <a href="{{ route('admin.articles.create') }}" class="btn btn-rounded btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>
                        @endpermission
                    </div>
                    <div class="table-responsive">
                        <table id="myTable" class="table w-100">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('app.image')</th>
                                <th>@lang('app.title')</th>
                                <th>@lang('app.slug')</th>
                                <th>@lang('app.excerpt')</th>
                                <th>@lang('app.category')</th>
                                <th>@lang('app.status')</th>
                                <th>{{__('app.published_at')}}</th>
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
                ajax: '{!! route('admin.articles.index') !!}',
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [
                    { data: 'DT_RowIndex'},
                    { data: 'image', name: 'image' },
                    { data: 'title', name: 'title' },
                    { data: 'slug', name: 'slug' },
                    { data: 'excerpt', name: 'excerpt' },
                    { data: 'category_id', name: 'category_id' },
                    { data: 'status', name: 'status' },
                    { data: 'published_at', name: 'published_at' },
                    { data: 'action', name: 'action' }
                ]
            });
            new $.fn.dataTable.FixedHeader( table );

            $('body').on('change', '.role_id', function () {
                const user_id = $(this).data('user-id');
                const role_id = $(this).val();

                $.easyAjax({
                    url: '{{ route('admin.employee.changeRole') }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', user_id, role_id },
                    container: '#myTable',
                    success: function (response) {
                        if (response.status === 'success') {
                            table.fnDraw();
                        }
                    }
                })
            })

            $('body').on('click', '.delete-row', function(){
                var id = $(this).data('row-id');
                swal({
                    icon: "warning",
                    buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title: "@lang('errors.areYouSure')",
                    text: "@lang('errors.deleteWarning')",
                }).then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('admin.employee.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.unblockUI();
                                    table._fnDraw();
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
