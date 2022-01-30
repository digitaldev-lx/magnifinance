@extends('layouts.master')

@push('head-css')
    <style>
        .module_name label
        {
            margin-bottom: -0.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12 col-md-2 mb-4 mt-3 mb-md-0 mt-md-0">
            <a class="nav-link" href="{{ route('admin.settings.index') }}">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                &nbsp; @lang('app.settings')
            </a>
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
            aria-orientation="vertical">
                <a class="nav-link active" href="#admin-setting" data-toggle="tab" id="admin-tab">@lang('app.adminModuleSettings')</a>
                <a class="nav-link" href="#employee-setting" data-toggle="tab" id="employee-tab">@lang('app.employeeModuleSettings')</a>
            </div>
        </div>
        <div class="col-12 col-md-10">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content">

                                <div class="active tab-pane" id="admin-setting">
                                    <form class="form-horizontal ajax-form" id="admin-form" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="col-md-12"><h5><i class="nav-icon icon-settings" aria-hidden="true"></i> @lang('app.module') @lang('app.settings')</h5> </div>

                                        <div class="col-md-12 m-2 mt-2 alert alert-primary">
                                            @lang('modules.package.updateOrgSettings')
                                        </div>

                                        <div class="row mx-2 mt-4 mb-5">
                                            <div class="col-md-12">
                                                <h6>@lang('modules.package.adminModuleSettings')</h6>
                                                <p>@lang('modules.package.adminModuleNote').</p>
                                                <hr>
                                            </div>

                                            @forelse ($package_modules as $package_module)
                                                <div class="col-md-3 module_name mt-5">
                                                    <label for="" class="">{{$package_module}}</label>
                                                    <label class="switch ml-3">
                                                        <input @if(in_array($package_module, $admin_modules)) checked @endif  value="{{$package_module}}" type="checkbox" name="package_module" class="package_module" data-module-name="{{ $package_module }}" data-type="administrator">
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            @empty
                                                <div class="col-md-12 module_name">
                                                    <p>@lang('modules.package.noModuleAvailable')</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </form>
                                </div>
                                <!-- /.tab-pane -->


                                <div class="tab-pane" id="employee-setting">
                                    <form class="form-horizontal ajax-form" id="employee-form" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="col-md-12"><h5><i class="nav-icon icon-settings" aria-hidden="true"></i>@lang('app.module') @lang('app.settings')</h5> </div>

                                        <div class="col-md-12 m-2 mt-2 alert alert-primary">
                                            @lang('modules.package.updateEmployeeSettings')
                                        </div>

                                        <div class="row mx-2 mt-4 mb-5">
                                            <div class="col-md-12">
                                                <h6>@lang('modules.package.employeeModuleSetting')</h6>
                                                <p>@lang('modules.package.selectModule')</p>
                                                <hr>
                                            </div>
                                            @forelse ($package_modules as $package_module)
                                                <div class="col-md-3 module_name mt-5">
                                                    <label for="" class="">{{$package_module}}</label>
                                                    <label class="switch ml-3">

                                                        <input @if(in_array($package_module, $employee_modules)) checked @endif  value="{{$package_module}}" type="checkbox" name="package_module" class="package_module" data-module-name="{{ $package_module }}" data-type="employee">

                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            @empty
                                                <div class="col-md-12 module_name">
                                                    <p>@lang('modules.package.noModulesAvailable')</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </form>
                                </div>
                                <!-- /.tab-pane -->
                                <p class="text-center">@lang('modules.package.refreshPage')</p>
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>

    </div>
@endsection

@push('footer-js')
    <script src="{{ asset('/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('assets/ace/ace.js') }}" type="text/javascript" charset="utf-8"></script>
    <script>
        $(function () {

            $('body').on('click', '#v-pills-tab a', function(e) {
                e.preventDefault();
                $(this).tab('show');
                $("html, body").scrollTop(0);
            });

            // store the currently selected tab in the hash value
            $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
                var id = $(e.target).attr("href").substr(1);
                window.location.hash = id;
            });

            // on load of the page: switch to the currently selected tab
            var hash = window.location.hash;
            $('#v-pills-tab a[href="' + hash + '"]').tab('show');
        });

        // package_module
        $('body').on('change', '.package_module', function() {
            var module_name = $(this).data('module-name');
            var user_type = $(this).data('type');
            var url = "{{route('admin.updateModuleSetting')}}";
            var status = $(this).is(':checked') ? 'active' : 'deactive';

            $.easyAjax({
                url: url,
                type: "POST",
                data: {'_method': 'PUT', '_token': "{{ csrf_token() }}", 'status': status, 'module_name':module_name, 'user_type':user_type}
            })
        });

    </script>
@endpush
