<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        {{-- SEO Meta Tags --}}
        <meta name="description" content="АкватЭрия - продажа сантехники в Челябинске с 2001 года. Широкий ассортимент качественной сантехники, комплектующих и запчастей. Доставка, установка, гарантия до 5 лет.">
        <meta name="keywords" content="сантехника челябинск, купить сантехнику, санитарно-техническое оборудование, комплектующие для сантехники, запчасти сантехника, доставка сантехники, установка сантехники">
        <meta name="author" content="ИП Нурисламова Наталья Владимировна">
        <meta name="robots" content="index, follow">
        <meta name="googlebot" content="index, follow">
        <meta name="theme-color" content="#1e40af">

        {{-- Open Graph Meta Tags --}}
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="АкватЭрия">
        <meta property="og:locale" content="ru_RU">
        <meta property="og:image" content="{{ asset('favicon.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">

        {{-- Twitter Card Meta Tags --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="{{ asset('favicon.png') }}">

        {{-- Business Information --}}
        <meta name="geo.region" content="RU-CHE">
        <meta name="geo.placename" content="Челябинск">
        <meta name="geo.position" content="55.160;61.402">
        <meta name="ICBM" content="55.160, 61.402">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="/favicon.png" sizes="any">
        <link rel="favicon" href="/favicon.png">
        <link rel="canonical" href="{{ url()->current() }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/js/app.js'])
        @inertiaHead

        {{-- Structured Data - Organization --}}
        @php
        $organizationData = [
            '@context' => 'https://schema.org',
            '@type' => 'Store',
            'name' => 'АкватЭрия',
            'description' => 'Продажа сантехники и санитарно-технического оборудования в Челябинске',
            'url' => url('/'),
            'logo' => asset('favicon.png'),
            'image' => asset('favicon.png'),
            'telephone' => '+7-951-235-32-26',
            'email' => 'qwer-75@mail.ru',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'ул. Работниц, 89/1, павильон 3306',
                'addressLocality' => 'Челябинск',
                'addressRegion' => 'Челябинская область',
                'addressCountry' => 'RU'
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => '55.160',
                'longitude' => '61.402'
            ],
            'openingHoursSpecification' => [
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                    'opens' => '08:30',
                    'closes' => '17:30'
                ]
            ],
            'priceRange' => '₽₽',
            'foundingDate' => '2001',
            'slogan' => 'Территория воды и тепла'
        ];

        $localBusinessData = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => 'ИП Нурисламова Наталья Владимировна',
            'alternateName' => 'АкватЭрия',
            'url' => url('/'),
            'telephone' => '+7-951-235-32-26',
            'email' => 'qwer-75@mail.ru',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'ул. Работниц, 89/1',
                'addressLocality' => 'Челябинск',
                'postalCode' => '454000',
                'addressCountry' => 'RU'
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => '55.160',
                'longitude' => '61.402'
            ],
            'taxID' => '744808080440',
            'legalName' => 'ИП Нурисламова Наталья Владимировна'
        ];
        @endphp

        <script type="application/ld+json">
        {!! json_encode($organizationData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>

        {{-- Structured Data - Local Business --}}
        <script type="application/ld+json">
        {!! json_encode($localBusinessData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
