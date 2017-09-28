<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.css') }}" rel="stylesheet">

</head>
<body>
<div id="app" >
    <div class="sm-"></div>
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">
            </div>
        </div>
    </nav>

    @yield('content')

</div>

<!-- Scripts -->
<script>
    window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
            ]) !!};
</script>
<script src="{{ asset('js/app.js') }}"></script>

</body>
</html>
