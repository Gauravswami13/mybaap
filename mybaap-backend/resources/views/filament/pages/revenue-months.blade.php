<x-filament-panels::page>
    <div class="ft-page">
        <div class="rpt-head">
            <h1>Month-wise revenue</h1>
        </div>

        <div class="grid grid-4">
            <article class="card stat">
                <div class="label">Active subscriptions</div>
                <div class="value">{{ $kpis['active_subscriptions'] }}</div>
            </article>
            <article class="card stat">
                <div class="label">Expiring (7 days)</div>
                <div class="value">{{ $kpis['expiring'] }}</div>
            </article>
            <article class="card stat">
                <div class="label">This month</div>
                <div class="value">{{ $revenue['this_month'] }}</div>
            </article>
            <article class="card stat">
                <div class="label">All time</div>
                <div class="value">{{ $revenue['total'] }}</div>
            </article>
        </div>

        <section class="mt">
            <h3 class="dash-h">Revenue Overview</h3>
            <div class="dash-rev">
                <div class="rev-card kpi-tone-brand">
                    <div class="kpi-ico">💰</div>
                    <div>
                        <div class="kpi-label">Total Revenue (All Time)</div>
                        <div class="kpi-value">{{ $revenue['total'] }}</div>
                    </div>
                </div>
                <div class="rev-card kpi-tone-ok">
                    <div class="kpi-ico">📈</div>
                    <div>
                        <div class="kpi-label">This Month Revenue</div>
                        <div class="kpi-value">{{ $revenue['this_month'] }}</div>
                    </div>
                </div>
                <div class="rev-card kpi-tone-info">
                    <div class="kpi-ico">📉</div>
                    <div>
                        <div class="kpi-label">Last Month Revenue</div>
                        <div class="kpi-value">{{ $revenue['last_month'] }}</div>
                    </div>
                </div>
                <div class="rev-card kpi-tone-warn">
                    <div class="kpi-ico">🗓️</div>
                    <div>
                        <div class="kpi-label">Months</div>
                        <div class="kpi-value">{{ $months->count() }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="card mt">
            <h3>Month-wise revenue</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Subscriptions</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($months as $row)
                            <tr>
                                <td><strong>{{ $row['label'] }}</strong></td>
                                <td>{{ $row['subscriptions'] }}</td>
                                <td>{{ $row['revenue'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="muted">No monthly revenue yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="rpt-table-foot muted">Showing {{ $months->count() ? '1' : '0' }} to {{ $months->count() }} of {{ $months->count() }} entries</p>
        </section>
    </div>
</x-filament-panels::page>
