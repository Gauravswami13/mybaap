@extends('site.layout')
@section('title', 'Login · MyBaap')
@section('content')
    <article class="page">
        <h1>Login</h1>
        <p class="muted">Enter the mobile number you use in the MyBaap app.</p>
        <form method="post" action="{{ route('login') }}">
            @csrf
            <label class="field">Mobile
                <input name="mobile" inputmode="numeric" value="{{ old('mobile') }}" placeholder="10 digit mobile" required>
            </label>
            @error('mobile')<p class="flash">{{ $message }}</p>@enderror
            <button class="btn" type="submit">Send OTP</button>
        </form>
    </article>
@endsection
