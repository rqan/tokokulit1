@props([
    'title' => 'TOKO RAFI
    'description' => 'Temukan koleksi busana eksklusif, gaun mewah, dan pakaian kustom terbaik di TOKO RAFI
    'image' => asset('images/og-default.jpg'),
    'url' => url()->current(),
    'type' => 'website'
])

<!-- Standard Meta Tags -->
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="robots" content="index, follow">

<!-- OpenGraph / WhatsApp Meta Tags -->
<meta property="og:site_name" content="TOKO RAFI">
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">

