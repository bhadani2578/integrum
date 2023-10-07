<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

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
        <link rel="shortcut icon" type="image/x-icon" href="assets/img/Integrum-Favicon-New-150x150.png">

        <title>Login | Truck Management system</title>

        <!-- vendor css -->
        <link href="lib/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
        <link href="lib/ionicons/css/ionicons.min.css" rel="stylesheet">
        <link href="lib/jqvmap/jqvmap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
        <!-- DashForge CSS -->
        <link rel="stylesheet" href="assets/css/dashforge.css">
        <link rel="stylesheet" href="assets/css/dashforge.dashboard.css">
        <link rel="stylesheet" href="assets/css/custom.css">
    </head>
    <body>
        @yield('content')
    </body>
</html>
