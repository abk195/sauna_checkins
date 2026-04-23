@extends('layouts.auth')

@section('title', 'Log in')

@section('heading', 'Admin login')

@section('content')
    <div class="card">
        <form method="post" action="{{ route('login') }}">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>
            <div class="field" style="display:flex;align-items:center;gap:0.5rem;">
                <input id="remember" type="checkbox" name="remember" value="1">
                <label for="remember" style="margin:0;">Remember me</label>
            </div>
            <button type="submit" class="btn" style="width:100%;">Log in</button>
        </form>
        <p style="margin-top: 1.25rem; text-align: center;">
            <a href="{{ route('checkin') }}" style="color: #94a3b8; font-size: 0.9rem;">← Back to check-in</a>
        </p>
    </div>
@endsection
