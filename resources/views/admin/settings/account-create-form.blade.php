<div class="modal-header">
    <h4>@lang('modules.paymentCredential.activationForm.title')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <section class="mt-3 mb-3">
        <form class="form-horizontal ajax-form" id="accountLinkCreate" method="POST">
            @csrf

            <h5 class="text-primary">@lang('modules.paymentCredential.activationForm.businessDetails')</h5>
            <div class="row">
                <div class="col-md-6">
                    <!-- text input -->
                    <div class="form-group">
                        <label for="name">
                            @lang('modules.paymentCredential.activationForm.name')
                            <sup class="text-danger">
                                <i class="fa fa-star"></i>
                            </sup>
                        </label>
                        <input type="text" class="form-control form-control-lg" name="name" id="name">
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- text input -->
                    <div class="form-group">
                        <label for="email">
                            @lang('modules.paymentCredential.activationForm.email')
                            <sup class="text-danger">
                                <i class="fa fa-star"></i>
                            </sup>
                        </label>
                        <input type="email" class="form-control form-control-lg" name="email" id="email">
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <!-- text input -->
                    <div class="form-group">
                        <label for="businessName">
                            @lang('modules.paymentCredential.activationForm.businessName')
                            <sup class="text-danger">
                                <i class="fa fa-star"></i>
                            </sup>
                        </label>
                        <input type="text" class="form-control form-control-lg" name="business_name" id="businessName">
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- text input -->
                    <div class="form-group">
                        <label for="businessType">
                            @lang('modules.paymentCredential.activationForm.businessType')
                            <sup class="text-danger">
                                <i class="fa fa-star"></i>
                            </sup>
                        </label>
                        <select name="business_type" id="businessType" class="form-control select2">
                            <option value="individual">@lang('modules.paymentCredential.activationForm.individual')
                            </option>
                            <option value="partnership">@lang('modules.paymentCredential.activationForm.partnership')
                            </option>
                            <option value="proprietorship">
                                @lang('modules.paymentCredential.activationForm.proprietorship')</option>
                            <option value="public_limited">
                                @lang('modules.paymentCredential.activationForm.publicLimited')</option>
                            <option value="private_limited">
                                @lang('modules.paymentCredential.activationForm.privateLimited')</option>
                            <option value="trust">@lang('modules.paymentCredential.activationForm.trust') </option>
                            <option value="society">@lang('modules.paymentCredential.activationForm.society')</option>
                            <option value="educational_institutes">
                                @lang('modules.paymentCredential.activationForm.educationalInstitutes')</option>
                            <option value="llp">@lang('modules.paymentCredential.activationForm.llp')</option>
                            <option value="ngo">@lang('modules.paymentCredential.activationForm.ngo')</option>
                            <option value="not_yet_registered">
                                @lang('modules.paymentCredential.activationForm.notYetRegistered')</option>
                            <option value="other">@lang('modules.paymentCredential.activationForm.other')</option>
                        </select>
                    </div>
                </div>
            </div>

            <h5 class="text-primary">@lang('modules.paymentCredential.activationForm.bankAccountDetails')</h5>
            <div class="row">
                <div class="col-md-6">
                    <!-- text input -->
                    <div class="form-group">
                        <label for="beneficiaryName">
                            @lang('modules.paymentCredential.activationForm.beneficiaryName')
                            <sup class="text-danger">
                                <i class="fa fa-star"></i>
                            </sup>
                        </label>
                        <input type="text" class="form-control form-control-lg" name="beneficiary_name"
                            id="beneficiaryName">
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- text input -->
                    <div class="form-group">
                        <label for="branchIFSCCode">
                            @lang('modules.paymentCredential.activationForm.branchIFSCCode')
                            <sup class="text-danger">
                                <i class="fa fa-star"></i>
                            </sup>
                        </label>
                        <input type="text" class="form-control form-control-lg" name="ifsc_code" id="branchIFSCCode">
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <!-- text input -->
                    <div class="form-group">
                        <label for="accountNumber">
                            @lang('modules.paymentCredential.activationForm.accountNumber')
                            <sup class="text-danger">
                                <i class="fa fa-star"></i>
                            </sup>
                        </label>
                        <input type="password" class="form-control form-control-lg" name="account_number"
                            id="accountNumber">
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- text input -->
                    <div class="form-group">
                        <label for="confirmAccountNumber">
                            @lang('modules.paymentCredential.activationForm.confirmAccountNumber')
                            <sup class="text-danger">
                                <i class="fa fa-star"></i>
                            </sup>
                        </label>
                        <input type="password" class="form-control form-control-lg" name="account_number_confirmation"
                            id="confirmAccountNumber">
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="form-group">
                        <span class="mb-2">
                            <input type="checkbox" name="tnc_accepted" id="tncAccepted">
                            <label for="tncAccepted">
                                @lang('modules.paymentCredential.activationForm.tncAgreement')
                                <a href="https://razorpay.com/terms/" target="_blank">
                                    @lang('modules.paymentCredential.activationForm.tnc')
                                </a>
                            </label>
                        </span>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="save-form" class="btn btn-success"><i class="fa fa-check"></i>
        @lang('app.submit')</button>
</div>

<script>
    $('.select2').select2();

    $('body').on('click', '#save-form', function() {
        $.easyAjax({
            url: "{{ route('front.razorpay.createAccount') }}",
            type: 'POST',
            container: '#accountLinkCreate',
            data: $('#accountLinkCreate').serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    $('#application-lg-modal').modal('hide');
                    $('#razorpay-get-started').addClass('d-none')
                    $('#razor-account-id-display').removeClass('d-none')
                    $('#razor-account-id-display').find('span').html(response.details.account_id)

                    $('#razor-status').find('span').html("{{ __('app.connected') }}")
                }

                if (response.status == 'fail') {
                    $('#' + response.error_name).closest('.form-group').append(
                        '<div class="invalid-feedback">' + response.message + '</div>')
                    $('#' + response.error_name).closest('.form-group').find('.form-control')
                        .addClass("is-invalid")
                }
            }
        })
    })

</script>
