<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    @vite(['resources/js/app.js'])
</head>
<body class="font-sans antialiased h-full" id="app">
@if (Auth::check())
    @php
        $user_auth_data = [
            'isLoggedIn' => true,
            'user' =>  \App\Http\Resources\UserResource::make(Auth::user()),
            'permissions' => auth()->user()->getAllPermissions()->flatten()->pluck('name')->toArray()
        ];
    @endphp
@else
    @php
        $user_auth_data = [
            'isLoggedIn' => false,
            'permissions' => []
        ];
    @endphp
@endif
<script>
    window.Laravel = JSON.parse(atob('{{ base64_encode(json_encode($user_auth_data)) }}'));
</script>
</body>
</html>
