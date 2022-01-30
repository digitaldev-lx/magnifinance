@extends('layouts.master')
@push('head-css')
<style>
  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 32px;
}
.select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 37px;
}
.select2-container .select2-selection--single {
    height: calc(2.875rem + 2px);
}
</style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.edit') @lang('app.location')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="createForm" class="ajax-form" method="POST"
                        onkeydown="return event.key != 'Enter';">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>@lang('app.location') @lang('app.name')</label>
                                    <input type="text" class="form-control form-control-lg" name="name"
                                        value="{{ $location->name }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.location') @lang('app.country')</label>
                                    <div class="input-group form-group">
                                        <select name="country_id" id="country_id" class="form-control select2">
                                            <option value="">@lang('app.select') @lang('app.location')</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}" {{ $country->id == $location->country_id ? 'selected' : '' }}>
                                                    {{ '+' . $country->phonecode . ' - ' . $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="button" id="save-form" class="btn btn-success btn-light-round"><i
                                            class="fa fa-check"></i> @lang('app.save')</button>
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
                url: '{{ route('superadmin.locations.update', $location->id) }}',
                container: '#createForm',
                type: "POST",
                redirect: true,
                file: true,
                data: $('#createForm').serialize()
            })
        });

    </script>
@endpush
