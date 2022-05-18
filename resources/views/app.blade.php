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
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
          rel="stylesheet">
    <link rel="manifest" href="manifest.json">
    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" href="{{ mix('css/chat.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Scripts -->
    @routes
    <script>

        function GET() {
            var data = [];
            for(let x = 0; x < arguments.length; ++x){
                if(location.href.match(new RegExp("/\?".concat(arguments[x],"=","([^\n&]*)"))) !== null){
                    data.push(location.href.match(new RegExp("/\?".concat(arguments[x],"=","([^\n&]*)")))[1])
                }
            }
            return data;
        }
    </script>
    <script src="{{ mix('js/app.js') }}" defer></script>
    <script type="text/javascript" src="/js/ckfinder/ckfinder.js"></script>
    <script>
        CKFinder.config( { connectorPath: route('ckfinder_connector') } );
    </script>
</head>
<body class="font-sans antialiased">
@include('ckfinder::setup')
<div id="confirm"></div>
@inertia

@env ('local')
    <script src="http://localhost:3000/browser-sync/browser-sync-client.js"></script>
@endenv
</body>
</html>
