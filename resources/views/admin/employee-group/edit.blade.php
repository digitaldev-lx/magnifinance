@extends('layouts.master')

@section('content')
<style>
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #999;
    }
    .select2-dropdown .select2-search__field:focus, .select2-search--inline .select2-search__field:focus {
        border: 0px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        margin: 0 13px;
    }
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #cfd1da;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__clear {
        cursor: pointer;
        float: right;
        font-weight: bold;
        margin-top: 8px;
        margin-right: 15px;
    }
     .select2 {
        width: 100%;
    }
</style>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.edit') @lang('menu.employee')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="createForm"  class="ajax-form" method="POST" onkeydown="return event.key != 'Enter';">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.name')</label>
                                    <input type="text" class="form-control form-control-lg" name="name" value="{{ ucwords($employeeGroup->name) }}">
                                </div>

                                <div class="form-group">
                                    <label>@lang('app.assignServices')</label>
                                    <select name="business_service_id[]" id="business_service_id" class="form-control form-control-lg select2" multiple="multiple">
                                        <option value="0">@lang('app.selectServices')</option>
                                        @foreach($business_services as $business_service)
                                            <option @if(in_array($business_service->id, $selectedServices)) selected @endif value="{{ $business_service->id }}">{{ $business_service->name }} ( {{ $business_service->location->name }} ) </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="button" id="save-form" class="btn btn-success btn-light-round">
                                        <i class="fa fa-check"></i> @lang('app.save')
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
@endsection

@push('footer-js')
<script>
    $('body').on('click', '#save-form', function() {
        $.easyAjax({
            url: '{{route('admin.employee-group.update', $employeeGroup->id)}}',
            container: '#createForm',
            type: "PUT",
            redirect: true,
            data: $('#createForm').serialize()
        })
    });
</script>
@endpush
