<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>{{env('APP_NAME')}}</title>

        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    </head>

    <body>
        @if (Auth::check())
        <script>
            window.openGDR = {!!json_encode([
            'isLoggedin' => true
        ])!!}
        </script>
        @else
        <script>
            window.openGDR = {!!json_encode(['isLoggedin' => false])!!}
            @isset($email)
                window.openGDR.recoverPassword = {!!json_encode(['email' => $email])!!}
            @endisset
        </script>
        @endif
        <div id="app">
        </div>
    </body>

</html>
