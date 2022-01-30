<head>
  <style type="text/css" title="x-apple-mail-formatting"></style>
  <meta name="viewport" content="width = 375, initial-scale = -1">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta charset="UTF-8">
  <title></title>
  <style>
     /* -------------------------------------
     RESPONSIVENESS
     !importants in here are necessary :/
     ------------------------------------- */
     @media only screen and (max-device-width: 700px) {
     .table-wrapper {
     margin-top: 0px !important;
     border-radius: 0px !important;
     }
     .header {
     border-radius: 0px !important;
     }
     }
  </style>
</head>
<body style="-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:none;margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size:100%;line-height:1.6">
  <table style="background: #F5F6F7;" width="100%" cellpadding="0" cellspacing="0">
     <tbody>
        <tr>
           <td>
              <!-- body -->
              <table cellpadding="0" cellspacing="0" class="table-wrapper" style="margin:auto;margin-top:50px;border-radius:7px;-webkit-border-radius:7px;-moz-border-radius:7px;max-width:700px !important;box-shadow:0 8px 20px #e3e7ea !important;-webkit-box-shadow:0 8px 20px #e3e7ea !important;-moz-box-shadow:0 8px 20px #e3e7ea !important;box-shadow: 0 8px 20px #e3e7ea !important; -webkit-box-shadow: 0 8px 20px #e3e7ea !important; -moz-box-shadow: 0 8px 20px #e3e7ea !important;">
                 <tbody>
                    <tr>
                       <!-- Brand Header -->
                        <td class="container" bgcolor="#FFFFFF" style="display:block !important;margin:0 auto !important;clear:both !important; background-image: url({{ asset('front/images/email/confirm_email_header_background.png') }});  height: 162px;background-position: center;background-size: cover;">
                           <h1 style="color:#fff;margin-top:20px; padding: 62px 42px;">@lang('email.newCompanyRegistered')</h1>
                        </td>
                       <!-- <td class="container" bgcolor="#FFFFFF" style="display:block !important;margin:0 auto !important;clear:both !important">
                          <img src="{{ asset('front/images/email/confirm_email_header_background.png') }}" style="max-width:100%">
                       </td> -->
                    </tr>
                    <tr>
                       <td class="container content" bgcolor="#FFFFFF" style="padding:35px 40px;border-bottom-left-radius:6px;border-bottom-right-radius:6px;display:block !important;margin:0 auto !important;clear:both !important">
                          <!-- content -->
                          <div class="content-box" style="max-width:600px;margin:0 auto;display:block">
                             <!--
                                Email template: confirm_device

                                Description: This email is sent to confirm a new device when an account signs in

                                 -->
                             <!-- Content -->
                             <div class="header-title">
                                <h1 style="font-family:&quot;Helvetica Neue&quot;, Helvetica, Arial, &quot;Lucida Grande&quot;, sans-serif;margin-bottom:15px;color:#47505E;margin:0px 0 10px;line-height:1.2;font-weight:200;font-size:28px;font-weight:bold;margin-bottom:30px">@lang('email.newCompanyAddedBy') {{ $user->name }} </h1>
                             </div>
                             <p style="font-weight:normal;padding:0;font-family:&quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height:1.7;margin-bottom:1.3em;font-size:15px;color:#47505E;font-size:16px;margin-bottom:0">
                                @lang('email.companyDetailsAreFollowing') :
                             </p>
                             <ul class="list" style="font-weight:normal;padding:0;font-family:&quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height:1.7;margin-bottom:1.3em;font-size:15px;color:#47505E">
                                <li style="margin-left:5px;list-style-position:inside;line-height:2.5"><b>@lang('app.name')</b> : {{ $user->name }}</li>
                                <li style="margin-left:5px;list-style-position:inside;line-height:2.5"><b>@lang('app.companyBusinessIndividualName') </b> : {{ $user->company->company_name }}</li>
                                <li style="margin-left:5px;list-style-position:inside;line-height:2.5"><b>@lang('app.businessEmail') </b>: <a href="mailto:" dir="ltr" x-apple-data-detectors="true" x-apple-data-detectors-type="link" x-apple-data-detectors-result="0">{{ $user->company->company_email }}</a></li>
                                <li style="margin-left:5px;list-style-position:inside;line-height:2.5"><b>@lang('app.businessContact')</b> : {{ $user->company->company_phone }}</li>
                                <li style="margin-left:5px;list-style-position:inside;line-height:2.5"><b>@lang('app.businessWebsite') </b>: {{ $user->company->website }}</li>
                                <li style="margin-left:5px;list-style-position:inside;line-height:2.5"><b>@lang('app.address')</b> : {{ $user->company->address }}</li>
                             </ul>
                             <br><br>
                             <center>
                                <a href=" {{ route('login') }} " style="text-decoration:none; color:#47505E">
                                <span class="confirmation-code" style="max-width:120px;max-height:42px;border:1px solid #DBDBDB;border-radius:5px;font-size:13px;text-transform:uppercase;padding:18px 30px"><strong>@lang('email.reviewProfile')</strong></span>
                                </a>
                             </center>
                             <br>
                          </div>
                          <!-- /content -->
                       </td>
                       <td>
                       </td>
                    </tr>
                 </tbody>
              </table>
              <!-- /body -->
              <div class="footer" style="padding-top:30px;padding-bottom:55px;width:100%;text-align:center;clear:both !important">
                 <p style="font-weight:normal;padding:0;font-family:&quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height:1.7;margin-bottom:1.3em;font-size:15px;color:#47505E;font-size:16px;margin-bottom:0;font-size:12px;color:#666;margin-top:0px">© 2021 {{ $globalSetting->company_name }}™</p>
                 <p class="social-icons" style="font-weight:normal;padding:0;font-family:&quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height:1.7;margin-bottom:1.3em;font-size:15px;color:#47505E;font-size:16px;margin-bottom:0;font-size:12px;color:#666;padding-top:5px">
                @php
                    $images = [
                        'facebook' => 'fb',
                        'twitter' => 'twitter',
                        'instagram' => 'insta',
                        'pinterest' => 'pinterest',
                        'linkedin' => 'linkedin'
                    ]
                @endphp
                @foreach ($socialLinks as $link)
                    <a href="{{ $link['link'] }}" style="color:#1EA69A;word-wrap:break-word;font-family:&quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-weight:800;color:#999;color:#049075 !important"><img width="25" src="{{ $images[$link['name']] == 'linkedin' ? 'https://icon-library.com/images/linkedin-icon-white-png/linkedin-icon-white-png-14.jpg' : 'https://cdn2.hubspot.net/hubfs/677576/email-'.$images[$link['name']].'.png' }}" style="max-width:100%"></a>
                @endforeach
                 </p>
              </div>
           </td>
        </tr>
     </tbody>
  </table>
</body>
