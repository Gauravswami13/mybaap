<a class="poster" href="{{ url('/videos/'.$video['id']) }}">
    <div class="thumb-wrap">
        <div class="thumb" @if($video['thumbnail']) style="background-image:url('{{ $video['thumbnail'] }}')" @endif></div>
        <span class="tag">{{ $video['is_paid'] ? 'PAID' : 'FREE' }}</span>
    </div>
    <p>{{ $video['title'] }}</p>
</a>
