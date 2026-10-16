<x-filament-widgets::widget>
    <section class="ft-page">
        <h3 class="dash-h">Revenue Overview</h3>
        <div class="dash-rev">
            <a class="rev-card rev-cta kpi-tone-brand" href="{{ $links['total'] }}">
                <div class="kpi-ico">💰</div>
                <div>
                    <div class="kpi-label">Total Revenue (All Time)</div>
                    <div class="kpi-value">{{ $revenue['total'] }}</div>
                </div>
            </a>
            <a class="rev-card rev-cta kpi-tone-ok" href="{{ $links['this_month'] }}">
                <div class="kpi-ico">📈</div>
                <div>
                    <div class="kpi-label">This Month Revenue</div>
                    <div class="kpi-value">{{ $revenue['this_month'] }}</div>
                </div>
            </a>
            <a class="rev-card rev-cta kpi-tone-info" href="{{ $links['last_month'] }}">
                <div class="kpi-ico">📉</div>
                <div>
                    <div class="kpi-label">Last Month Revenue</div>
                    <div class="kpi-value">{{ $revenue['last_month'] }}</div>
                </div>
            </a>
            <a class="rev-card rev-cta kpi-tone-warn" href="{{ $links['months'] }}">
                <div class="kpi-ico">🗓️</div>
                <div>
                    <div class="kpi-label">All Months Revenue</div>
                    <strong>View month-wise revenue breakdown →</strong>
                </div>
            </a>
        </div>
    </section>
</x-filament-widgets::widget>
