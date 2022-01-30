@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-center justify-content-md-end mb-3">
                        <a href="javascript:showNewTodoForm();" class="btn btn-rounded btn-primary mb-1"><i class="fa fa-plus"></i> @lang('app.createNew')</a>
                    </div>
                    <div class="table-responsive">
                        <table id="todo-table" class="table w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('app.title')</th>
                                    <th>@lang('app.status')</th>
                                    <th class="text-right">@lang('app.action')</th>
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
        function showNewTodoForm() {
            let url = "{{ route('admin.todo-items.create') }}"
            $(modal_default + ' ' + modal_heading).html('...');
            $.ajaxModal(modal_default, url);
        }

        function showUpdateTodoForm(id) {
            let url = "{{ route('admin.todo-items.edit', ':id') }}"
            url = url.replace(':id', id);

            $(modal_default + ' ' + modal_heading).html('...');
            $.ajaxModal(modal_default, url);
        }

        function updateTodoStatus(id) {
            const title = $('#status-'+id).data('title');
            let url = "{{route('admin.todo-items.update', ':id')}}"
            url = url.replace(':id', id);

            let data = {
                _token: '{{ csrf_token() }}',
                _method: 'PUT',
                id: id,
                status: $('#status-'+id).val(),
                title: title
            }

            $.easyAjax({
                url: url,
                container: '#todo-table',
                type: "POST",
                data: data,
                success: function (response) {
                    if(response.status == 'success'){
                        $.unblockUI();
                        todoTable._fnDraw();
                    }
                }
            })
        }

        function deleteTodoItem(id) {
            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            })
            .then((willDelete) => {
                if (willDelete) {
                    let url = "{{ route('admin.todo-items.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    let data = {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    }

                    $.easyAjax({
                        url,
                        data,
                        type: 'POST',
                        container: '#roleMemberTable',
                        success: function (response) {
                            if (response.status == 'success') {
                                $.unblockUI();
                                todoTable._fnDraw();
                            }
                        }
                    })
                }
            });
        }

        var todoTable = $('#todo-table').dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{!! route('admin.todo-items.index') !!}',
            language: languageOptions(),
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            order: [[0, 'DESC']],
            columns: [
                { data: 'DT_RowIndex'},
                { data: 'title', name: 'title' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', width: '20%' }
            ]
        });
        new $.fn.dataTable.FixedHeader( todoTable );

        $('body').on('click', '#create-todo-item', function () {
            $.easyAjax({
                url: "{{route('admin.todo-items.store')}}",
                container: '#createTodoItem',
                type: "POST",
                data: $('#createTodoItem').serialize(),
                success: function (response) {
                    if(response.status == 'success'){
                        $.unblockUI();
                        todoTable._fnDraw();

                        $(modal_default).modal('hide');
                    }
                }
            })
        });

        $('body').on('click', '#update-todo-item', function () {
            const id = $(this).data('id');
            let url = "{{route('admin.todo-items.update', ':id')}}"
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                container: '#editTodoItem',
                type: "POST",
                data: $('#editTodoItem').serialize(),
                success: function (response) {
                    if(response.status == 'success'){
                        $.unblockUI();
                        todoTable._fnDraw();

                        $(modal_default).modal('hide');
                    }
                }
            })
        });
    </script>
@endpush
