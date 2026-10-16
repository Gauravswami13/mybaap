@extends('site.layout')
@section('title', 'Account · MyBaap')
@section('content')
    <article class="page">
        <h1>{{ $user->name ?: 'MyBaap' }}</h1>
        <p class="muted">{{ $user->mobile }}</p>
        @if($subscription)
            <p>Active plan: {{ $subscription->plan?->name }} until {{ $subscription->end_date?->toDateString() }}</p>
        @else
            <p class="muted">No active plan.</p>
        @endif
        <div class="menu">
            <a href="{{ route('plans') }}">Plans</a>
            <a href="{{ route('history') }}">Subscription history</a>
            <a href="{{ route('notifications') }}">Notifications</a>
            <a href="{{ route('faq') }}">FAQ</a>
            <a href="{{ route('profile.edit') }}">Edit profile</a>
            <a href="{{ url('/privacy-policy') }}">Privacy</a>
            <a href="{{ url('/terms') }}">Terms</a>
        </div>
        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button class="btn" type="submit">Logout</button>
        </form>
        <form method="post" action="{{ route('account.delete') }}" style="margin-top:16px">
            @csrf
            <label class="field"><input type="checkbox" name="confirm" value="1" required> Delete my account</label>
            <button class="btn" type="submit">Delete account</button>
        </form>
    </article>
@endsection
