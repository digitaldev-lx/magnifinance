<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="description" content="">
      <meta name="keywords" content="">
      <title>@lang('app.pageNotFound')</title>
      <!-- Styles -->
      <link href="{{ asset('front-assets/css/page.css') }}" rel="stylesheet">
      <link href="{{ asset('front-assets/css/style.css') }}" rel="stylesheet">
      <style>
         .not_found_img img {
         width: 800px;
         height: 500px;
         }
      </style>
   </head>
   <body class="layout-centered bg-white">
      <!-- Main Content -->
      <main class="main-content text-center pb-lg-8">
         <div class="container">
            <div class="not_found_img position-relative">
               <img src="{{ asset('front/images/404.jpg') }}" alt="Image" />
            </div>
            <br>
            <button class="btn btn-secondary w-150 mr-2 go-back" type="button">@lang('app.goBack')</button>
         </div>
      </main>
      <!-- /.main-content -->
      <!-- Scripts -->
      <script src="{{ asset('front-assets/js/page.min.js') }}"></script>
      <script src="{{ asset('front-assets/js/script.js') }}"></script>
      <script>
        $('body').on('click', '.go-back', function() {
            var url = "{{ route('front.index') }}";
	        window.location.href = url;
        });
    </script>
   </body>
</html>
