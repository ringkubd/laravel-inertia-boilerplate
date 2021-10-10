<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="author" content="Anwar Jahid">

{{--        <title>{{ config('app.name', 'Laravel') }}</title>--}}

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <!-- Scripts -->
        @routes
        <script src="{{ mix('js/app.js') }}" defer></script>
        <script type="text/javascript" src="/js/ckfinder/ckfinder.js"></script>
        <script>
            CKFinder.config( { connectorPath: route('ckfinder_connector') } );
        </script>
    </head>
    <body class="font-sans antialiased">
    @include('ckfinder::setup')
        @inertia

        @env ('local')
{{--            <script src="http://localhost:3000/browser-sync/browser-sync-client.js"></script>--}}
        @endenv
    </body>
</html>
