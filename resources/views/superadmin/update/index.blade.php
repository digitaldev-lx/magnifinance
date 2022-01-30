@extends('layouts.master')

@push('head-css')
    <link href="{{asset('assets/plugins/swal/sweetalert.css')}}" rel="stylesheet">
    <style>
        .modal.show {
            padding-right: 0px !important;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12 col-md-2 mb-4 mt-3 mb-md-0 mt-md-0 nav-pills">
            <a class="nav-link mb-2" href=" {{ route('superadmin.settings.index') }}">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> @lang('app.back')
            </a>
            <a class="nav-link active mb-2" href="#">
            @lang('app.updateApplication')
            </a>
        </div>
        <div class="col-12 col-md-10">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                    <div class="col-12">
                        <div class="active tab-pane" id="update">
                            <h4 class="mb-10">@lang('menu.updateApp')</h4>
                            @include('vendor.froiden-envato.update.update_blade')
                            @include('vendor.froiden-envato.update.version_info')
                            @include('vendor.froiden-envato.update.changelog')
                            <!--/row-->
                        </div>
                    </div>
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
    </div>
@endsection

@push('footer-js')
    @include('vendor.froiden-envato.update.update_script')
@endpush
