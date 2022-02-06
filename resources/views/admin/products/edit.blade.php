@extends('layouts.master')

@push('head-css')
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
        #discounted-price {
            font-size: 1.5rem;
        }
    </style>
@endpush


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">@lang('app.edit') @lang('app.service')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form role="form" id="createForm"  class="ajax-form" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="id" value="{{ $product->id }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.service') @lang('app.name')</label>
                                    <input type="text" name="name" id="name" value="{{ $product->name }}" class="form-control form-control-lg" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('app.location')</label>
                                    <div class="input-group">
                                        <select name="location_id" id="location_id" class="form-control form-control-lg">
                                            @foreach($locations as $location)
                                                <option
                                                        @if($location->id == $product->location_id) selected @endif
                                                        value="{{ $location->id }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('app.service') @lang('app.description')</label>
                                    <textarea name="description" id="description" cols="30" class="form-control-lg form-control" rows="4">{{ $product->description }}</textarea>
                                </div>
                            </div>

                            @if (!is_null($taxes) || !is_null($selectedTax))
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>@lang('app.select') @lang('app.tax')</label>
                                        <select name="tax_ids[]" id="tax_ids" class="form-control form-control-lg">
                                            <option value="0">@lang('app.select') @lang('app.tax')</option>
                                            @foreach($taxes as $tax)
                                                <option @if(in_array($tax->id, $selectedTax)) selected @endif value="{{ $tax->id }}">{{ $tax->name }} {{ $tax->percent }}%</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('app.price')</label>
                                    <input type="number" step="0.01" min="0" name="price" id="price" class="form-control form-control-lg" value="{{ $product->price }}"  />
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('modules.services.discount')</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-lg" id="discount" name="discount" min="0" value="{{ $product->discount }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary dropdown-toggle" id="discount-type-select" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang('modules.services.'.$product->discount_type)</button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item discount_type" data-type="percent" href="javascript:;">@lang('modules.services.percent')</a>
                                                <a class="dropdown-item discount_type" data-type="fixed" href="javascript:;">@lang('modules.services.fixed')</a>
                                            </div>
                                        </div>
                                        <input type="hidden" id="discount-type" name="discount_type" value="{{ $product->discount_type }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 offset-md-1">
                                <div class="form-group">
                                    <label>@lang('modules.services.discountedPrice')</label>
                                    <p class="form-control-static" id="discounted-price">--</p>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div id="file-upload-box" >
                                    <div class="row" id="file-dropzone">
                                        <div class="col-md-12">
                                            <div class="dropzone"
                                                    id="file-upload-dropzone">
                                                {{ csrf_field() }}
                                                <div class="fallback">
                                                    <input name="file" type="file" multiple/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="text-danger">@lang('modules.theme.recommendedResolutionNote')</h6>

                                <input type="hidden" name="productID" id="productID">

                                <div class="form-group">
                                    <label for="">@lang('app.status')</label>
                                    <select name="status" id="" class="form-control form-control-lg">
                                        <option @if($product->status == 'active') selected @endif
                                        value="active">@lang('app.active')</option>
                                        <option @if($product->status == 'inactive') selected @endif
                                        value="inactive">@lang('app.inactive')</option>
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
        $(function () {
            $('#description').summernote({
                dialogsInBody: true,
                height: 300,
                toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['fontsize', ['fontsize']],
                ['para', ['ul', 'ol', 'paragraph']],
                ["view", ["fullscreen"]]
            ]
            })

            $('#discount-type').val('{{ $product->discount_type }}');
            $('#time-type').val('{{ $product->time_type }}');
        })
        var mockFile = {!! $images !!};
        var defaultImage = '';
        var lastIndex = 0;

        Dropzone.autoDiscover = false;
        //Dropzone class
        myDropzone = new Dropzone("#file-upload-dropzone", {
            url: "{{ route('admin.products.updateImages') }}",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            paramName: "file",
            maxFilesize: 10,
            maxFiles: 10,
            acceptedFiles: "image/*",
            autoProcessQueue: false,
            uploadMultiple: true,
            addRemoveLinks:true,
            parallelUploads:10,
            init: function () {
                myDropzone = this;
            },
            dictDefaultMessage: "@lang('app.dropzone.defaultMessage')",
            dictRemoveFile: "@lang('app.dropzone.removeFile')"
        });

        myDropzone.on('sending', function(file, xhr, formData) {
            var id = $('#productID').val();

            formData.append('product_id', id);
            if (mockFile.length > 0) {
                formData.append('uploaded_files', JSON.stringify(mockFile));
            }
            formData.append('default_image', defaultImage);
        });

        myDropzone.on('addedfile', function (file) {
            var index = mockFile.findIndex(x => x.name == file.name);

            if (index === -1) {
                index = lastIndex + 1;
            }
            lastIndex = index;

            var div = document.createElement('div');
            div.className = 'form-check form-check-inline';
            var input = document.createElement('input');
            input.className = 'form-check-input';
            input.type = 'radio';
            input.name = 'default_image';
            input.id = 'default-image-'+index;
            input.value = file.name;
            if ('{{ $product->default_image }}' == file.name) {
                input.checked = true;
            }
            div.appendChild(input);
            var label = document.createElement('label');
            label.className = 'form-check-label';
            label.innerHTML = "@lang('app.dropzone.makeDefaultImage')";
            label.htmlFor = 'default-image-'+index;
            div.appendChild(label);
            file.previewTemplate.appendChild(div);
        })

        myDropzone.on('removedfile', function (file) {
            var index = mockFile.findIndex(x => x.name == file.name);
            mockFile.splice(index, 1);
        })

        // Create the mock file:
        mockFile.forEach(file => {
            var path = "{{ asset_url('product/'.$product->id.'/:file_name') }}";
            path = path.replace(':file_name', file.name);

            myDropzone.emit('addedfile', file);

            myDropzone.emit('thumbnail', file, path);

            myDropzone.files.push(file);
            myDropzone.emit("complete", file);
        });

        myDropzone.options.maxFiles = myDropzone.options.maxFiles - mockFile.length;

        myDropzone.on("maxfilesexceeded", function(file) { this.removeFile(file); });

        const createSlug = function (str) {
            str = str.replace(/^\s+|\s+$/g, ''); // trim
            str = str.toLowerCase();

            // remove accents, swap ñ for n, etc
            let from = "ÁÄÂÀÃÅČÇĆĎÉĚËÈÊẼĔȆÍÌÎÏŇÑÓÖÒÔÕØŘŔŠŤÚŮÜÙÛÝŸŽáäâàãåčçćďéěëèêẽĕȇíìîïňñóöòôõøðřŕšťúůüùûýÿžþÞĐđßÆa·/_,:;";
            let to = "AAAAAACCCDEEEEEEEEIIIINNOOOOOORRSTUUUUUYYZaaaaaacccdeeeeeeeeiiiinnooooooorrstuuuuuyyzbBDdBAa------";
            for (let i = 0, l = from.length; i < l; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }

            str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-'); // collapse dashes

            $('#slug').val(str);
        };

        $('body').on('keyup', '#name', function() {
            createSlug($(this).val());
        });

        $('body').on('click', '.discount_type', function() {
            var type = $(this).data('type');

            $('#discount-type-select').html(type);
            $('#discount-type').val(type);
            calculateDiscountedPrice();
        });

        $('body').on('click', '#save-form', function() {
            $.easyAjax({
                url: "{{route('admin.products.update', $product->id)}}",
                container: '#createForm',
                type: "POST",
                redirect: true,
                file:true,
                data: $('#createForm').serialize(),
                success: function (response) {
                    productID = response.productID;
                    $('#productID').val(response.productID);
                    defaultImage = response.defaultImage;

                    if (myDropzone.getQueuedFiles().length > 0) {
                        myDropzone.processQueue();
                    }
                    else{
                        var blob = new Blob();
                        blob.upload = { 'chunked': myDropzone.defaultOptions.chunking };
                        myDropzone.uploadFile(blob);
                    }
                    var msgs = "@lang('messages.updatedSuccessfully')";
                    $.showToastr(msgs, 'success');
                    window.location.href = '{{ route('admin.products.index') }}'
                }
            })
        });

        $('body').on('keyup', '#discount, #price', function() {
            calculateDiscountedPrice();
        });

        $('body').on('change', '#discount, #price', function() {
            calculateDiscountedPrice();
        });

        $('body').on('wheel', '#discount, #price', function() {
            calculateDiscountedPrice();
        });

        function calculateDiscountedPrice() {
            var price = $('#price').val();
            var discount = $('#discount').val();
            var discountType = $('#discount-type').val();

            if (discountType == 'percent') {
                if(discount > 100){
                    $('#discount').val(100);
                    discount = 100;
                }
            } else {
                if (parseInt(discount) > parseInt(price)) {
                    $('#discount').val(price);
                    discount = price;
                }
            }

            var discountedPrice = price;

            if(discount >= 0 && discount >= '' && price != '' && price > 0){
                if (discountType == 'percent') {
                    discountedPrice = parseFloat(price) - (parseFloat(price) * (parseFloat(discount) / 100));
                } else {
                    discountedPrice = parseFloat(price) - parseFloat(discount);
                }
            }

            if(discount != '' && price != '' && price > 0){
                $('#discounted-price').html(discountedPrice.toFixed(2));
            } else {
                $('#discounted-price').html('--');
            }
        }

        calculateDiscountedPrice();

    </script>

@endpush
