@extends('site.layout')
@section('title', $title.' · MyBaap')
@section('content')
    <article class="page" style="max-width:960px">
        <h1>{{ $title }}</h1>
        <video controls autoplay playsinline src="{{ $url }}"></video>
        <p class="muted">This link expires in 5 minutes. Refresh the page if playback stops.</p>
    </article>
@endsection
