<form class="form-horizontal ajax-form" id="vendorPageForm" method="POST" enctype='multipart/form-data'>
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleInputPassword1">@lang('app.ogImage')</label>
                <div class="card">
                    <div class="card-body">
                        <input type="file" id="og_image" name="og_image" accept=".png,.jpg,.jpeg" class="dropify"
                            data-default-file="{{ $vendorPage->og_image }}" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tax_name" class="control-label">@lang('app.company') @lang('app.primary')
                            @lang('app.phone')</label>
                        <input type="text" class="form-control  form-control-lg" id="primary_contact"
                            name="primary_contact" value="{{ $vendorPage->primary_contact }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tax_name" class="control-label">@lang('app.company') @lang('app.secondary')
                            @lang('app.phone')</label>
                        <input type="text" class="form-control  form-control-lg" id="secondary_contact"
                            name="secondary_contact" value="{{ $vendorPage->secondary_contact }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="exampleInputPassword1">@lang('app.company') @lang('app.address')</label>
                        <textarea class="form-control form-control-lg" name="address" id="address" cols="30"
                            rows="5">{!! $vendorPage->address !!}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">@lang('app.company') @lang('app.description')</label>
                <textarea name="description" id="description" class="form-control-lg form-control "
                    rows="3">{{ $vendorPage->description }}</textarea>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">@lang('app.company') @lang('app.seo')
                    @lang('app.keywords')</label>
                <input type="text" class="form-control form-control-lg" id="seo_keywords" name="seo_keywords"
                    data-role="tagsinput" value="{{ $vendorPage->seo_keywords }}" />
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">@lang('app.company') @lang('app.seo')
                    @lang('app.description')</label>
                <textarea name="seo_description" id="seo_description" cols="30" class="form-control-lg form-control"
                    rows="3">{{ $vendorPage->seo_description }}</textarea>
            </div>
        </div>

        <div class="col-md-12">
            <label class="control-label">@lang('app.company')
                @lang('app.gallery')</label>
            <div id="file-upload-box">
                <div class="row" id="file-dropzone">
                    <div class="col-md-12">
                        <div class="dropzone" id="file-upload-dropzone">
                            {{ csrf_field() }}
                            <div class="fallback">
                                <input name="file" type="file" multiple />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 mt-2">
            <h5 class="class="text-primary"">@lang('app.showMapOption')</h5>
                <div class="form-group">
                    <label class="control-label">@lang("app.allowMapOption")</label>
                    @if ($superadmin->map_option == 'deactive')
                    <br>
                    <label class="control-label text-danger">@lang("app.superAdminAllowMapMessage")</label>
                    @endif
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="map_option" id="map_option"
                        {{($vendorPage->map_option == 'active' && $superadmin->map_option == 'active')?'checked':''}} {{$superadmin->map_option == 'deactive'?'disabled':''}} value="active">
                        <span class="slider round"></span>
                    </label>
                </div>
        </div>
        <div class="col-md-12  {{($vendorPage->map_option == 'deactive'||$superadmin->map_option == 'deactive')?'d-none':''}}" id="map_key_option">
            <div class="row">
<!--                <div class="col-md-12">
                    <div class="form-group">
                        <label for="tax_name" class="control-label">@lang('app.company') @lang('app.location')</label>
                        <input type="text" class="form-control form-control-lg" id="location">
                    </div>
                </div>-->
                <input type="hidden" class="form-control  form-control-lg" id="latitude" name="latitude"
                    value="{{$lat_long['latitude']}}">
                <input type="hidden" class="form-control  form-control-lg" id="longitude" name="longitude"
                    value="{{$lat_long['longitude']}}">

                <div class="col-md-12 p-3">
                        <div class="googlemap" id="map"></div>
                        <label class="control-label text-danger">@lang("app.superAdminAllowMapDevMessage")</label>

                </div>
            </div>
        </div>

    </div>

    <div class="row mt-2">
        <div class="col-md-12">
            <div class="form-group">
                <button id="saveVendorPage" type="button" class="btn btn-success"><i class="fa fa-check"></i>
                    @lang('app.save')</button>
            </div>
        </div>
    </div>

</form>
