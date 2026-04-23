<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @isset($manifestId)
        <meta name="checkin-manifest-id" content="{{ $manifestId }}">
        <meta name="checkin-manifest-name" content="{{ $manifestName ?? '' }}">
    @endisset
    <title>{{ config('app.name', 'Sauna Bookings') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="checkin-body">
    <div id="app"></div>
</body>
</html>
