<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        table {
            border-collapse: collapse;
            font-size: 12px;
        }

        table, td, th {
            border: 0.5px solid #6A726C;
        }
        td,th{
            padding: 5px;
            margin: 2px!important;
        }
        @page { margin: 60px 25px;}
        /*body { margin:0px auto; }*/
        header {
            position: fixed;
            top: -30px;
            left: 0px;
            right: 0px;
            height: 50px;
            /** Extra personal styles **/
            /*background-color: #377FAF;*/
            color: black;
            text-align: center;
            line-height: 5px;
        }
        footer {
            position: fixed;
            bottom: -35px;
            left: 0px;
            right: 0px;
            height: 40px;
            /** Extra personal styles **/
            background-color: whitesmoke;
            color: black;
            text-align: center;
            line-height: 20px;
        }
        body{
            margin: 10px auto;
        }
    </style>
    @yield('style')


    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100">
    <!-- Page Heading -->
    <header class="bg-white shadow">
        <div class="max-w-7xl m-0 p-0">
            @yield('header')
        </div>
    </header>

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>
</div>
<script type="text/php">
			if ( isset($pdf) ) {
				{{-- //$abc=new \Dompdf\FontMetrics
			$font = \Dompdf\FontMetrics::get_font("helvetica", "bold"); --}}
    echo $pdf->page_text(665, 40, "Page: {PAGE_NUM} of {PAGE_COUNT}  "." | Date : ".date('d-m-Y h:i'), 'helvetica', 6, array(0,0,0));

    }
</script>
</body>
</html>
