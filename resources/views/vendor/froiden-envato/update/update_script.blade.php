<!-- Custom Theme JavaScript -->

<script type="text/javascript">
    var updateAreaDiv = $('#update-area');
    var refreshPercent = 0;
    var checkInstall = true;

    $('body').on('click', '#update-app', function () {
        if ($('#update-frame').length) {
            return false;
        }

        @php($envatoUpdateCompanySetting = \Froiden\Envato\Functions\EnvatoUpdate::companySetting())

        @if(!is_null($envatoUpdateCompanySetting->supported_until) && \Carbon\Carbon::parse($envatoUpdateCompanySetting->supported_until)->isPast())

            var supportText = `Your support has been expired on <b><span id='support-date'>{{\Carbon\Carbon::parse($envatoUpdateCompanySetting->supported_until)->format('d M, Y')}}</span></b><br>Please renew your suport for one-click updates.`;

            const wrappedText = document.createElement('div');
            wrappedText.innerHTML = supportText;

            swal({
                icon: "warning",
                buttons: ["Cancel", "Renew Now"],
                dangerMode: true,
                title: "Support Expired",
                html:true,
                content: wrappedText,
            }).then((isConfirm) => {
                if (isConfirm) {
                    window.open(
                    "{{ config('froiden_envato.envato_product_url') }}",
                    '_blank' // <- This is what makes it open in a new window.
                    );
                }
            });

        @else

            var inputText = `<div class='alert alert-danger'>Do not click update now button if the application is customised. Your changes will be lost.\n
                    <br>
                    Take backup of files and database before updating. \
                    <br>
                    <strong><i>Author will not be responsible if something goes wrong</i></strong>
                    </div>
                    <span class="">To confirm if you have read the above message, type <strong><i>confirm</i></strong> in the field.</span>
                    <div class="swal-content">
                        <input id="confirm" name="confirm" class="swal-content__input" type="text">
                    </div>
                    `;

            const wrapper = document.createElement('div');
            wrapper.innerHTML = inputText;

            var confirmText = '';

            swal({
                icon: "success",
                title: "Are you sure?",
                buttons: ["No, cancel please!", "Yes, update it!"],
                dangerMode: true,
                html:true,
                content: wrapper,
            }).then((isConfirm) => {
                if (isConfirm != true){
                    return false;
                }

                if(confirmText.toLowerCase() !== "confirm") {
                    swal({
                        icon: "error",
                        dangerMode: true,
                        title: "Text not matched",
                        text: "You have entered wrong spelling on confirm",
                    });
                    return false;
                }
                if (confirmText.toLowerCase() === "confirm") {
                    updateAreaDiv.removeClass('hide');
                    swal.close();
                    $.easyAjax({
                        type: 'GET',
                        url: '{!! route("admin.updateVersion.update") !!}',
                        success: function (response) {
                            if(response.status =='success'){
                                downloadScript();
                                downloadPercent();
                            }else{
                                swal({
                                    icon: "error",
                                    dangerMode: true,
                                    title: response.message,
                                });
                            }

                        }
                    });
                }
            });

            $("#confirm").change(function() {
                confirmText = $("#confirm").val();
            });

        @endif

    })

    function downloadScript() {
        $.easyAjax({
            type: 'GET',
            url: '{!! route("admin.updateVersion.download") !!}',
            success: function (response) {
                clearInterval(refreshPercent);
                $('#percent-complete').css('width', '100%');
                $('#percent-complete').html('100%');
                $('#download-progress').append("<i><span class='text-success'>Download complete.</span> Now Installing...Please wait (This may take few minutes.)</i>");

                window.setInterval(function () {
                    /// call your function here
                    if (checkInstall == true) {
                        checkIfFileExtracted();
                    }
                }, 1500);

                installScript();

            }
        });
    }

    function getDownloadPercent() {
        $.easyAjax({
            type: 'GET',
            url: '{!! route("admin.updateVersion.downloadPercent") !!}',
            success: function (response) {
                response = response.toFixed(1);
                $('#percent-complete').css('width', response + '%');
                $('#percent-complete').html(response + '%');
            }
        });
    }

    function checkIfFileExtracted() {
        $.easyAjax({
            type: 'GET',
            url: '{!! route("admin.updateVersion.checkIfFileExtracted") !!}',
            success: function (response) {
                checkInstall = false;
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        });
    }

    function downloadPercent() {
        updateAreaDiv.append('<hr><div id="download-progress">' +
            'Download Progress<br><div class="progress progress-lg">' +
            '<div class="progress-bar progress-bar-success active progress-bar-striped" role="progressbar" id="percent-complete" role="progressbar""></div>' +
            '</div>' +
            '</div>'
        );
        //getting data
        refreshPercent = window.setInterval(function () {
            getDownloadPercent();
            /// call your function here
        }, 1500);
    }

    function installScript() {
        $.easyAjax({
            type: 'GET',
            url: '{!! route("admin.updateVersion.install") !!}',
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        });
    }

    function getPurchaseData() {
        var token = "{{ csrf_token() }}";
        $.easyAjax({
            type: 'POST',
            url: "{{ route('purchase-verified') }}",
            data: {'_token': token},
            container: "#support-div",
            messagePosition: 'inline',
            success: function (response) {
                window.location.reload();
            }
        });
        return false;
    }
</script>
