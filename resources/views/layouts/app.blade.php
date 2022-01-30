<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- CSRF Token -->
      <meta name="csrf-token" content="{{ csrf_token() }}">

      <!-- Favicon icon -->
      <link rel="icon" href="{{$frontThemeSettings->favicon_url}}" type="image/x-icon" />

      <link rel="manifest" href="{{ asset('favicon/manifest.json') }}">
      <meta name="msapplication-TileColor" content="#ffffff">
      <meta name="msapplication-TileImage" content="{{ asset('favicon/ms-icon-144x144.png') }}">
      <meta name="theme-color" content="#ffffff">
      <title>{{ ucwords($settings->company_name) }}</title>
      <!-- SEO -->
      <meta name='description' content='{{ $frontThemeSettings->seo_description}}' />
      <meta name='keywords' content='{{$frontThemeSettings->seo_keywords}}' />
      <!-- Scripts -->
      <script src="{{ asset('js/app.js') }}" defer></script>
      <!-- Fonts -->
      <link rel="dns-prefetch" href="https://fonts.gstatic.com">
      <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
      <!-- Styles -->
      <link rel="stylesheet" href="{{ asset('css/app.css') }}">
      <!-- Google Font: Source Sans Pro -->
      <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
      <style>
         :root {
         --main-color: {{ $themeSettings->primary_color }};
         --active-color: {{ $themeSettings->secondary_color }};
         --sidebar-bg: {{ $themeSettings->sidebar_bg_color }};
         --sidebar-color: {{ $themeSettings->sidebar_text_color }};
         }
         .login-page, .register-page{
            background: rgba(51, 51, 51, 0.27058823529411763);
         }
         html {
            background: url('{{ asset('img/login-bg.jpg') }}') no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
         }
         #img_fluid {
            max-width: 155px;
         }
      </style>
   </head>
   <body class="hold-transition login-page">
      <div class="login-box">
        <div class="login-logo">
            <a href="{{ route('login') }}">
                <img class="img-fluid" id="img_fluid" src="{{ $settings->logo_url }}">
            </a>
        </div>

        @yield('content')
      </div>
   </body>
</html>
