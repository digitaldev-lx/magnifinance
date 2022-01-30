<style>
    .add-members .select2-container--default, .add-members .select2-container--default .select2-search__field, .add-members .select2-container--default .select2-search--inline {
    width: 100% !important;
}
</style>

<div class="modal-header">
    <h4 class="modal-title">@lang('modules.rolePermission.manageMembers')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="table-responsive">
        <table id="roleMemberTable" class="table w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>@lang('modules.rolePermission.tables.memberName')</th>
                    <th>@lang('modules.rolePermission.tables.memberRole')</th>
                    <th class="text-right">@lang('app.action')</th>
                </tr>
            </thead>
        </table>
    </div>

    <hr>
    <form id="add-member-form" class="ajax-form">
        @csrf
        <div class="form-body">
            <h5>@lang('modules.rolePermission.addMember')</h5>
            <div class="row">
                <div class="col-sm-12 ">
                    <div class="form-group add-members">
                        <label>@lang('modules.rolePermission.members')</label>
                        <select class="form-control select2 select2-multiple" name="user_ids[]" id="user_ids" multiple="multiple" data-placeholder="@lang('modules.rolePermission.forms.addMembers')">
                            @foreach ($usersToAdd as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="save-add-member" class="btn btn-success"> <i class="fa fa-check"></i>
        @lang('app.submit')</button>
</div>

<script>
    $('#user_ids').select2({
        allowClear: true
    });

    function renderSelect() {
        $.easyAjax({
            url: '{{ route('admin.role-permission.getMembersToAdd', $id) }}',
            type: 'GET',
            success: function (response) {
                let options = '';
                response.usersToAdd.forEach(user => {
                    options += `<option value='${user.id}'>${user.name}</option>`;
                })

                $('#user_ids').html(options);
                $('#user_ids').select2();
            }
        })
    }

    roleMemberTable = $('#roleMemberTable').dataTable({
        destroy: true,
        responsive: true,
        processing: true,
        serverSide: true,
        pageLength: 3,
        lengthChange: false,
        ajax: '{!! route('admin.role-permission.getMembers', ['role_id' => $id]) !!}',
        language: languageOptions(),
        "fnDrawCallback": function( oSettings ) {
            $("body").tooltip({
                selector: '[data-toggle="tooltip"]'
            });
        },
        order: [[1, 'ASC']],
        columns: [
            { data: 'DT_RowIndex', searchable: false, orderable: false },
            { data: 'name', name: 'name' },
            { data: 'roles.display_name', name: 'roles.display_name', sortable: false },
            { data: 'action', name: 'action', width: '20%' }
        ]
    });

    $('body').on('click', '#save-add-member', function() {
        $.easyAjax({
            url: '{{ route('admin.role-permission.addMembers', ['role_id' => $id]) }}',
            type: 'POST',
            data: $('#add-member-form').serialize(),
            container: '#add-member-form',
            success: function (response) {
                if (response.status == 'success') {
                    roleMemberTable.fnDraw();
                    $('#user_ids').val(null).trigger('change');
                    table_modified = true;
                    renderSelect();
                }
            }
        })
    })

    $('body').on('click', '.delete-member', function () {
        const id = $(this).data('user-id');
        swal({
            icon: "warning",
            buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
            dangerMode: true,
            title: "@lang('errors.areYouSure')",
            text: "@lang('errors.deleteWarning')",
        }).then((willDelete) => {
            if (willDelete) {
                let url = '{{ route('admin.role-permission.removeMember') }}';

                let data = {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE',
                    user_id: id
                }

                $.easyAjax({
                    url,
                    data,
                    type: 'POST',
                    container: '#roleMemberTable',
                    success: function (response) {
                        if (response.status == 'success') {
                            roleMemberTable.fnDraw();
                            table_modified = true;
                            $('#user_ids').val(null).trigger('change');
                            renderSelect();
                        }
                    }
                })
            }
        });
    })
</script>
