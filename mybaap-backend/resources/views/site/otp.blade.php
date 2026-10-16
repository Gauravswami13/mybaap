@extends('site.layout')
@section('title', 'OTP · MyBaap')
@section('content')
    <article class="page">
        <h1>Enter OTP</h1>
        <p class="muted">Code sent to {{ $mobile }}.</p>
        <form method="post" action="{{ route('login.otp') }}">
            @csrf
            <input type="hidden" name="mobile" value="{{ $mobile }}">
            <label class="field">OTP
                <input name="otp" inputmode="numeric" maxlength="6" required>
            </label>
            @error('otp')<p class="flash">{{ $message }}</p>@enderror
            <button class="btn" type="submit">Verify</button>
        </form>
    </article>
@endsection
