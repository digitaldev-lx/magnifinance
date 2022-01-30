<h4>@lang('menu.rolesPermissions')</h4>
<div class="col-sm-12">
    <a href="javascript:;" id="addRole" class="btn btn-success btn-sm btn-outline waves-effect waves-light "><i class="fa fa-gear"></i> @lang("modules.rolePermission.addRole")</a>
</div>

@foreach($roles as $role)
    <div class="col-md-12 b-all mt-2">
        <div class="row bg-dark p-3 justify-content-center align-items-center">
            <div class="col-md-4">
                <h5 class="text-white mt-2 mb-2"><strong>{{ ucwords($role->display_name) }}</strong></h5>
            </div>
            <div class="col-md-4 text-center role-members">
                <button class="btn btn-xs btn-danger btn-rounded show-members" data-role-id="{{ $role->id }}"><i class="fa fa-users"></i> {{ $role->member_count  }} @lang('modules.rolePermission.members')</button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-default btn-sm btn-rounded pull-right" onclick="toggle('#role-permission-{{ $role->id }}')" data-role-id="{{ $role->id }}"><i class="fa fa-key"></i> @lang('modules.rolePermission.permissions')</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 b-t permission-section d-none" id="role-permission-{{ $role->id }}" >
                <table class="table ">
                    <thead>
                    <tr class="bg-white">
                        <th>
                            <div class="form-group d-flex">
                                <label class="switch mr-2">
                                    <input type="checkbox"
                                        @if(count($role->permissions) == $totalPermissions)
                                            checked
                                        @endif onchange="toggleAllPermissions({ roleId: {{ $role->id }}}, this);">
                                    <span class="slider round"></span>
                                </label>
                                @lang('modules.rolePermission.selectAll')
                            </div>
                        </th>
                        <th>@lang('app.add')</th>
                        <th>@lang('app.view')</th>
                        <th>@lang('app.update')</th>
                        <th>@lang('app.delete')</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $module)
                            <tr>
                                <td>{{ __('app.'.\Illuminate\Support\Str::camel($module->display_name)) }}

                                    @if($module->description != Lang::get($module->description) )
                                        <a class="mytooltip" data-toggle="tooltip" data-placement="top" title="@lang($module->description)">
                                            <i class="fa fa-info-circle"></i>
                                        </a>
                                    @endif
                                </td>

                                @foreach($module->permissions as $permission)
                                    <td>
                                        <label class="switch permissions">
                                            <input type="checkbox"
                                                @if($role->hasPermission([$permission->name]))
                                                    checked
                                                @endif value="active" onchange="togglePermission({ roleId: {{ $role->id }}, permissionId: {{ $permission->id }}}, this);" >
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                @endforeach

                                @if(count($module->permissions) < 4)
                                    @for($i=1; $i<=(4-count($module->permissions)); $i++)
                                        <td>&nbsp;</td>
                                    @endfor
                                @endif

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endforeach

@push('footer-js')
    <script>
        const create_form = `@include('admin.role-permission.create_form')`;
        let table_modified = false;

        function togglePermission(options, ele) {
            let assignPermission = 'no';

            if ($(ele).is(':checked')) {
                assignPermission = 'yes';
            }

            options = {...options, _token: '{{ csrf_token() }}', assignPermission};

            $.easyAjax({
                url: '{{ route('admin.role-permission.store') }}',
                type: 'POST',
                data: options,
            })
        }

        function toggleAllPermissions(options, ele) {
            let assignPermission = 'no';

            if ($(ele).is(':checked')) {
                assignPermission = 'yes';
            }

            options = {...options, _token: '{{ csrf_token() }}', assignPermission};

            $.easyAjax({
                url: '{{ route('admin.role-permission.toggleAllPermissions') }}',
                type: 'POST',
                data: options,
                success: function (response) {
                    if (response.status == 'success') {
                        $(`#role-permission-${ options.roleId } .permissions input`).each(function (index, input) {
                            if ($(ele).is(':checked') !== $(input).is(':checked')) {
                                $(input).prop('checked', $(ele).is(':checked'));
                            }
                        })
                    }
                }
            })
        }

        $('body').on('click', '#addRole', function() {
            const url = '{{ route('admin.role-permission.create') }}';

            $.ajaxModal('#application-lg-modal', url);
        })

        $('#application-lg-modal').on('hide.bs.modal', function (e) {
            if (table_modified) {
                window.location.reload();
            }
            else {
                $('#roleTable').DataTable().destroy();
            }
        })

        $('body').on('click', '#save-create-role', function () {
            var url = '{{ route("admin.role-permission.addRole")}}';

            $.easyAjax({
                url: url,
                type: 'POST',
                data: $('#create-role').serialize(),
                container: '#application-lg-modal',
                success: function (response) {
                    $('#create-edit-form').html(create_form);
                    roleTable.fnDraw();
                    table_modified = true;
                }
            })
        })

        $('body').on('click', '.edit-role', function () {
            var id = $(this).data('role-id');
            var url = '{{ route("admin.role-permission.edit", ":id")}}';
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                type: 'GET',
                container: '#application-lg-modal',
                success: function (response) {
                    $('#create-edit-form').html(response.view);
                }
            })
        })

        $('body').on('click', '#save-edit-role', function () {
            const id = $('#edit-role').data('role-id');
            var url = '{{ route("admin.role-permission.update", ":id")}}';
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                type: 'PUT',
                data: $('#edit-role').serialize(),
                container: '#application-lg-modal',
                success: function (response) {
                    $('#create-edit-form').html(create_form);
                    roleTable.fnDraw();
                    table_modified = true;
                }
            })
        })

        $('body').on('click', '#cancel-edit-role', function () {
            $('#create-edit-form').html(create_form);
        })

        $('body').on('click', '.delete-role', function () {
            const id = $(this).data('role-id');
            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            })
            .then((willDelete) => {
                if (willDelete) {
                    var url = '{{ route("admin.role-permission.destroy", ":id")}}';
                    url = url.replace(':id', id);
                    const _token = '{{ csrf_token() }}';

                    $.easyAjax({
                        url: url,
                        type: 'POST',
                        data: { _token, _method: 'DELETE' },
                        container: '#application-lg-modal',
                        success: function (response) {
                            roleTable.fnDraw();
                            table_modified = true;
                            $('#create-edit-form').html(create_form);
                        }
                    });
                }
            });
        })

        $('body').on('click', '.show-members', function() {
            const id = $(this).data('role-id');
            let url = '{{ route('admin.role-permission.show', ':id') }}';
            url = url.replace(':id', id);

            $.ajaxModal('#application-lg-modal', url);
        })
    </script>
@endpush
