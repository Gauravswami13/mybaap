@extends('site.layout')
@section('title', 'FAQ · MyBaap')
@section('content')
    <article class="page">
        <h1>FAQ</h1>
        @forelse($faqs as $faq)
            <div class="plan">
                <h2 style="margin-top:0">{{ $faq->question }}</h2>
                <p class="muted">{{ $faq->answer }}</p>
            </div>
        @empty
            <p class="muted">No questions yet.</p>
        @endforelse
    </article>
@endsection
