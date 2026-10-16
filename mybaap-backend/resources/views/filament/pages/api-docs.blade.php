@php
    $catalog = $this->catalog();
@endphp

<x-filament-panels::page>
    <div class="ft-page adoc">
        <section class="adoc-hero">
            <div>
                <p class="client-kicker">MyBaap API</p>
                <h1>API documentation</h1>
                <p>Har endpoint ka method, path, request body aur sample JSON response. Playback URL kabhi cache mat karna. Local OTP <span class="kbd">123456</span> sirf debug me chalta hai.</p>
            </div>
            <div class="adoc-kpis">
                <div><strong>{{ $catalog['total'] }}</strong><span>APIs</span></div>
                <div><strong>{{ count($catalog['groups']) }}</strong><span>Groups</span></div>
                <div><strong>v1</strong><span>Prefix /api/v1</span></div>
            </div>
        </section>

        <section class="card adoc-meta">
            <div>
                <h3>Base URL</h3>
                <div class="api-box">{{ $catalog['base'] }}</div>
            </div>
            <div>
                <h3>Headers</h3>
                <pre class="api-box">Accept: application/json
Content-Type: application/json
Authorization: Bearer TOKEN</pre>
            </div>
            <div>
                <h3>Success envelope</h3>
                <pre class="api-box">{
  "success": true,
  "message": "OK",
  "data": {}
}</pre>
            </div>
            <div>
                <h3>Error envelope</h3>
                <pre class="api-box">{
  "success": false,
  "message": "Validation failed.",
  "data": { "errors": {} }
}</pre>
                <p class="muted" style="margin-top:8px">Blocked users get <span class="kbd">403</span>. <span class="kbd">playback_path</span> response me kabhi nahi aata.</p>
            </div>
        </section>

        <div class="adoc-toolbar">
            <input class="input" type="search" data-adoc-search placeholder="Search method, path, field… e.g. playback, otp, Bearer">
            <nav class="adoc-toc">
                @foreach ($catalog['groups'] as $group)
                    <a href="#{{ $group['id'] }}">{{ $group['title'] }}</a>
                @endforeach
            </nav>
        </div>

        @foreach ($catalog['groups'] as $group)
            <section class="adoc-group" id="{{ $group['id'] }}" data-adoc-group>
                <h2>{{ $group['title'] }} <small>{{ count($group['endpoints']) }} APIs</small></h2>
                @if ($group['note'] !== '')
                    <p class="muted">{{ $group['note'] }}</p>
                @endif
                @foreach ($group['endpoints'] as $endpoint)
                    @php
                        $search = strtolower($endpoint['method'].' '.$endpoint['path'].' '.$endpoint['summary'].' '.implode(' ', $endpoint['lines']));
                    @endphp
                    <details class="adoc-ep" open data-adoc-ep data-search="{{ $search }}">
                        <summary>
                            <span class="adoc-m adoc-m-{{ strtolower($endpoint['method']) }}">{{ $endpoint['method'] }}</span>
                            <code>{{ $endpoint['path'] }}</code>
                            <span class="adoc-auth {{ $endpoint['auth'] ? 'need' : 'open' }}">{{ $endpoint['auth'] ? 'Bearer' : 'Public' }}</span>
                            <span class="muted adoc-sum">{{ str_replace('`', '', $endpoint['summary']) }}</span>
                        </summary>
                        <div class="adoc-body">
                            @foreach ($endpoint['lines'] as $line)
                                <p>{{ str_replace('`', '', $line) }}</p>
                            @endforeach
                            <p class="muted">Full URL: <span class="kbd">{{ $catalog['base'] }}{{ $endpoint['path'] }}</span>
                                @if ($endpoint['auth'])
                                    · Header <span class="kbd">Authorization: Bearer TOKEN</span>
                                @endif
                            </p>
                            @if ($endpoint['blocks'] !== [])
                                <div class="adoc-grid">
                                    @foreach ($endpoint['blocks'] as $block)
                                        <div>
                                            <h4>{{ $block['label'] }}</h4>
                                            <pre class="api-box">{{ $block['code'] }}</pre>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </details>
                @endforeach
            </section>
        @endforeach
        <p class="muted" data-adoc-empty hidden>No matching APIs.</p>
    </div>
    <script>
        (function () {
            const q = document.querySelector('[data-adoc-search]');
            const empty = document.querySelector('[data-adoc-empty]');
            if (!q) return;
            q.addEventListener('input', function () {
                const term = q.value.trim().toLowerCase();
                let shown = 0;
                document.querySelectorAll('[data-adoc-group]').forEach(function (group) {
                    let n = 0;
                    group.querySelectorAll('[data-adoc-ep]').forEach(function (ep) {
                        const ok = !term || (ep.getAttribute('data-search') || '').indexOf(term) !== -1;
                        ep.hidden = !ok;
                        if (ok) n++;
                    });
                    group.hidden = n === 0;
                    shown += n;
                });
                if (empty) empty.hidden = shown > 0;
            });
        })();
    </script>
</x-filament-panels::page>
