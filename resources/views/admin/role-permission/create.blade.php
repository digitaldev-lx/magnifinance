<div class="modal-header">
    <h4 class="modal-title">@lang('modules.rolePermission.manageRole')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="table-responsive">
        <table id="roleTable" class="table w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>@lang('modules.rolePermission.tables.roleName')</th>
                    <th>@lang('modules.rolePermission.tables.roleDescription')</th>
                    <th class="text-right">@lang('app.action')</th>
                </tr>
            </thead>
        </table>
    </div>
    <hr>
    <div id="create-edit-form">
        @include('admin.role-permission.create_form')
    </div>
</div>

<script>
    roleTable = $('#roleTable').dataTable({
        destroy: true,
        responsive: true,
        processing: true,
        serverSide: true,
        pageLength: 3,
        lengthChange: false,
        ajax: '{!! route('admin.role-permission.data') !!}',
        language: languageOptions(),
        "fnDrawCallback": function( oSettings ) {
            $("body").tooltip({
                selector: '[data-toggle="tooltip"]'
            });
        },
        order: [[1, 'ASC']],
        columns: [
            { data: 'DT_RowIndex', searchable: false, orderable: false },
            { data: 'display_name', name: 'display_name' },
            { data: 'description', name: 'description' },
            { data: 'action', name: 'action', width: '20%' }
        ]
    });
</script>
