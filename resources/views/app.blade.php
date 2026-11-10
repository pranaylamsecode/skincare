<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="At-home skincare by FMZ India. A certified woman specialist brings professional Nu Skin care to your home in Ahmedabad.">
    <meta name="theme-color" content="#7a2e2e">
    <title>FMZ Glow | At-Home Skincare by FMZ India</title>

    @php
        $siteFavicon = 'images/FMZ Glow logo.jpg';
        $siteLogo = 'images/FMZ Glow logo.jpg';
        try {
            $siteFavicon = \App\Models\Setting::get('site_favicon', $siteFavicon);
            $siteLogo = \App\Models\Setting::get('site_logo', $siteLogo);
        } catch (\Throwable $e) {
            // gracefully fallback if database or settings table is not ready
        }
    @endphp
    <!-- Dynamic Favicon & Brand Icons -->
    <link rel="icon" type="image/png" href="{{ asset($siteFavicon) }}">
    <link rel="shortcut icon" href="{{ asset($siteFavicon) }}">
    <link rel="apple-touch-icon" href="{{ asset($siteLogo) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Razorpay Checkout -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
