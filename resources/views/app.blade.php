<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="font-sans antialiased" id="app">
@if (Auth::check())
    @php
        $user_auth_data = [
            'isLoggedIn' => true,
            'user' =>  \App\Http\Resources\UserResource::make(Auth::user())
        ];
    @endphp
@else
    @php
        $user_auth_data = [
            'isLoggedIn' => false
        ];
    @endphp
@endif
<script>
    window.Laravel = JSON.parse(atob('{{ base64_encode(json_encode($user_auth_data)) }}'));
</script>
<script src="{{ mix('js/app.js') }}" type="text/javascript"></script>

</body>
</html>
