<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Twitter -->
    <meta name="twitter:site" content="@themepixels">
    <meta name="twitter:creator" content="@themepixels">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="DashForge">
    <meta name="twitter:description" content="Responsive Bootstrap 4 Dashboard Template">
    <meta name="twitter:image" content="http://themepixels.me/dashforge/img/dashforge-social.png">

    <!-- Facebook -->
    <meta property="og:url" content="http://themepixels.me/dashforge">
    <meta property="og:title" content="DashForge">
    <meta property="og:description" content="Responsive Bootstrap 4 Dashboard Template">

    <meta property="og:image" content="http://themepixels.me/dashforge/img/dashforge-social.png">
    <meta property="og:image:secure_url" content="http://themepixels.me/dashforge/img/dashforge-social.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="600">

    <!-- Meta -->
    <meta name="description" content="Responsive Bootstrap 4 Dashboard Template">
    <meta name="author" content="ThemePixels">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{url('assets/img/Integrum-Favicon-New-150x150.png')}}">

    <title>Integrum Energy</title>

    <!-- vendor css -->
    <link href="{{url('lib/@fortawesome/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
    <link href="{{url('lib/ionicons/css/ionicons.min.css')}}" rel="stylesheet">
    <link href="{{url('lib/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
    <link href="{{url('lib/prismjs/themes/prism-vs.css')}}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- DashForge CSS -->
    <link rel="stylesheet" href="{{url('assets/css/dashforge.css')}}">
    <link rel="stylesheet" href="{{url('assets/css/dashforge.dashboard.css')}}">
    <link rel="stylesheet" href="{{url('assets/css/dashforge.demo.css')}}">
    <link rel="stylesheet" href="{{url('assets/css/custom.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('assets/css/style.css')}}">

    <script src="{{url('lib/jquery/jquery.min.js')}}"></script>
    <link rel="stylesheet" href="{{ url('assets/css/toastr.min.css') }}">
    <script src="{{ url('assets/js/toastr.min.js') }}"></script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    @include('particals.left_sidebar')

    <div class="content ht-100v pd-0">
        @include('particals.header')
        @yield('content')
        @if(session()->has('showToast') && session('showToast'))
        @yield('js')
                @if(Session::has('messages'))
                    <script type="text/javascript">
                        $(document).ready(function() {
                            @foreach(Session::get('messages') AS $msg)
                                toastr['{{ $msg["type"] }}']('{{$msg["message"]}}');
                            @endforeach
                        });

                    </script>

                @endif
                @if(Session::has('success'))
                    <script type="text/javascript">
                        $(document).ready(function() {

                                toastr['success']('{{Session::get('success')}}');

                        });
                    </script>

                @endif

                @if (count($errors) > 0)
                    <script type="text/javascript">
                        $(document).ready(function() {
                            @foreach($errors->all() AS $error)
                                toastr['error']('{{$error}}');
                            @endforeach
                        });
                    </script>
                @endif
        @endif
</body>
</html>
