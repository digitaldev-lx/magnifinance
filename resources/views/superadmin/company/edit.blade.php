@extends('layouts.master')

@section('content')
    <div class="row">
    <div class="col-md-12">
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">@lang('app.edit') @lang('app.company')</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <form class="form-horizontal ajax-form" id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="company_name" class="control-label">@lang('app.company')
                            @lang('app.name')</label>
                            <input type="text" class="form-control  form-control-lg" id="company_name"
                            name="company_name" value="{{$company->company_name}}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="company_email" class="control-label">@lang('app.company')
                            @lang('app.email')</label>
                            <input type="text" class="form-control  form-control-lg" id="company_email"
                            name="company_email" value="{{$company->company_email}}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="company_phone" class="control-label">@lang('app.company')
                            @lang('app.phone')</label>
                            <input type="text" class="form-control  form-control-lg" id="company_phone"
                            name="company_phone" value="{{$company->company_phone}}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="input-file-now">@lang('app.logo')</label>
                            <div class="card">
                            <div class="card-body">
                                <input type="file" id="input-file-now" name="logo" accept=".png,.jpg,.jpeg" value="{{ $company->logo}}"
                                    class="dropify" data-default-file="{{ $company->logo_url }}" />
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address">@lang('app.address')</label>
                            <textarea class="form-control form-control-lg" name="address" id="address" cols="30"
                            rows="5">{{$company->address}}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_format" class="control-label">
                                @lang('app.date_format')
                                </label>
                                <select name="date_format" id="date_format"
                                    class="form-control form-control-lg select2">
                                @foreach($dateFormats as $key => $dateFormat)
                                <option @if($company->date_format === $key) selected @endif value="{{ $key }}">{{
                                $key.' ('.$dateObject->format($key).')' }}
                                </option>
                                @endforeach
                                </select>
                            </div>
                            </div>
                            <div class="col-md-6">
                            <div class="form-group">
                                <label for="time_format" class="control-label">
                                @lang('app.time_format')
                                </label>
                                <select name="time_format" id="time_format"
                                    class="form-control form-control-lg select2">
                                @foreach($timeFormats as $key => $timeFormat)
                                <option @if($company->time_format === $key) selected @endif value="{{ $key }}">{{
                                $key.' ('.$dateObject->format($key).')' }}
                                </option>
                                @endforeach
                                </select>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="website" class="control-label">@lang('app.company')
                            @lang('app.website')</label>
                            <input type="text" class="form-control form-control-lg" id="website" name="website"
                            value="{{$company->website}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="timezone" class="control-label">@lang('app.timezone')</label>
                            <select name="timezone" id="timezone" class="form-control form-control-lg select2">
                            @foreach($timezones as $tz)
                            <option @if($company->timezone === $tz) selected @endif>
                            {{ $tz }}
                            </option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="currency_id" class="control-label">@lang('app.currency')</label>
                            <select name="currency_id" id="currency_id" class="form-control  form-control-lg">
                            @foreach($currencies as $currency)
                            <option @if($company->currency_id === $currency->id) selected @endif
                            value="{{ $currency->id }}">
                            {{ $currency->currency_symbol.' ('.$currency->currency_code.')' }}
                            </option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="locale" class="control-label">@lang('app.language')</label>
                            <select name="locale" id="locale" class="form-control form-control-lg">
                            @forelse($enabledLanguages as $language)
                            <option @if($company->locale === $language->language_code) selected @endif
                            value="{{ $language->language_code }}">
                            {{ $language->language_name }}
                            </option>
                            @empty
                            <option value="en">English
                            </option>
                            @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">@lang('app.status')</label>
                            <select name="status" id="status" class="form-control form-control-lg">
                            <option @if($company->status == 'active') selected @endif
                            value="active">@lang('app.active')</option>
                            <option @if($company->status == 'inactive') selected @endif
                            value="inactive">@lang('app.inactive')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="verified">@lang('app.verification') @lang('app.status')</label>
                            <select name="verified" id="verified" class="form-control form-control-lg">
                            <option @if($company->verified == 'yes') selected @endif
                            value="yes">@lang('app.yes')</option>
                            <option @if($company->verified == 'no') selected @endif
                            value="no">@lang('app.no')</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <button id="save-form" type="button" class="btn btn-success"><i class="fa fa-check"></i>
                            @lang('app.update')</button>
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
        $('.dropify').dropify({
                messages: {
                    default: '@lang("app.dragDrop")',
                    replace: '@lang("app.dragDropReplace")',
                    remove: '@lang("app.remove")',
                    error: '@lang('app.largeFile')'
                }
            });

            $('body').on('click', '#save-form', function() {
                $.easyAjax({
                    url: '{{route('superadmin.companies.update', $company->id)}}',
                    container: '#editForm',
                    type: "POST",
                    redirect: true,
                    file:true,
                })
            });
    </script>
@endpush
