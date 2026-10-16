@php
    $shown = $report['by_plan']->count();
    $periods = [
        'this_month' => 'This month ('.now()->format('M Y').')',
        'last_month' => 'Last month ('.now()->subMonthNoOverflow()->format('M Y').')',
        '7' => 'Last 7 days',
        '30' => 'Last 30 days',
        'this_year' => 'This year ('.now()->format('Y').')',
    ];
@endphp

<x-filament-panels::page>
    <div class="ft-page">
        <div class="rpt-head">
            <h1>Platform reports</h1>
            <button class="rpt-export" type="button" onclick="window.print()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 3h8l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/><path d="M15 3v6h6"/><path d="M8 13h8M8 17h5"/></svg>
                Export PDF
            </button>
        </div>

        <div class="rpt-filters">
            <div>
                <div class="rpt-filter-label">Filter by category</div>
                <div class="rpt-cats">
                    @foreach (['all' => 'All', 'free' => 'Free', 'paid' => 'Paid'] as $key => $label)
                        <button
                            type="button"
                            class="rpt-cat {{ $category === $key ? 'active' : '' }}"
                            wire:click="$set('category', '{{ $key }}')"
                        >{{ $label }}</button>
                    @endforeach
                </div>
            </div>
            <div>
                <div class="rpt-filter-label">Time period</div>
                <label class="rpt-period">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
                    <select wire:model.live="period">
                        @foreach ($periods as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </div>

        <div class="rpt-charts" wire:key="rpt-{{ $period }}-{{ $category }}">
            <section class="card">
                <h3>New users per week</h3>
                <canvas id="rptWeekChart" height="220"></canvas>
            </section>
            <section class="card">
                <h3>Revenue by plan</h3>
                <canvas id="rptPlanChart" height="220"></canvas>
            </section>
            <section class="card">
                <h3>Free vs Paid</h3>
                <canvas id="rptMixChart" height="220"></canvas>
            </section>
        </div>

        <section class="card mt">
            <h3>Top plans by collection</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Plan</th>
                            <th>Category</th>
                            <th>Total collection (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($report['by_plan'] as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $row['plan'] }}</strong></td>
                                <td>{{ $row['type'] }}</td>
                                <td>{{ $row['revenue'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="muted">No plans in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="rpt-table-foot muted">Showing {{ $shown ? '1' : '0' }} to {{ $shown }} of {{ $shown }} entries</p>
        </section>
    </div>

    @script
    <script>
        const charts = window.__mybaapReportCharts || [];
        charts.forEach((chart) => chart.destroy());
        window.__mybaapReportCharts = [];

        const payload = @js($report['charts']);
        const brand = '#e50914';
        const mint = '#ff8a80';
        const ink = '#f4f4f4';
        const muted = '#c4c2c2';
        const axis = {
            color: muted,
            font: { size: 11, weight: '600' },
            grid: { color: '#163042' },
            ticks: { color: muted, font: { size: 11, weight: '600' } },
        };
        const inr = (value) => {
            const amount = Number(value) || 0;
            if (amount >= 100000) return '₹' + (amount / 100000).toFixed(1) + 'L';
            if (amount >= 1000) return '₹' + (amount / 1000).toFixed(0) + 'K';
            return '₹' + amount;
        };
        const draw = () => {
            window.__mybaapReportCharts.forEach((chart) => chart.destroy());
            window.__mybaapReportCharts = [
                new Chart(document.getElementById('rptWeekChart'), {
                    type: 'line',
                    data: {
                        labels: payload.weeks.labels,
                        datasets: [{
                            data: payload.weeks.values,
                            borderColor: brand,
                            backgroundColor: brand,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: brand,
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            tension: 0.15,
                            borderWidth: 3,
                        }],
                    },
                    options: {
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { ...axis, beginAtZero: true, title: { display: true, text: 'Users', color: muted, font: { size: 11, weight: '600' } }, ticks: { ...axis.ticks, precision: 0 } },
                            x: { ...axis, title: { display: true, text: payload.weeks.axis, color: muted, font: { size: 11, weight: '600' } } },
                        },
                    },
                }),
                new Chart(document.getElementById('rptPlanChart'), {
                    type: 'bar',
                    data: {
                        labels: payload.plans.labels.length ? payload.plans.labels : ['No plans'],
                        datasets: [{ data: payload.plans.values.length ? payload.plans.values : [0], backgroundColor: brand, borderRadius: 6, maxBarThickness: 64 }],
                    },
                    options: {
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { ...axis, beginAtZero: true, title: { display: true, text: 'Revenue (₹)', color: muted, font: { size: 11, weight: '600' } }, ticks: { ...axis.ticks, callback: inr } },
                            x: { ...axis, title: { display: true, text: 'Plan', color: muted, font: { size: 11, weight: '600' } } },
                        },
                    },
                }),
                new Chart(document.getElementById('rptMixChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Free', 'Paid'],
                        datasets: [{ data: [payload.mix.free || 0, payload.mix.paid || 0], backgroundColor: [brand, mint], borderWidth: 0, hoverOffset: 4 }],
                    },
                    options: {
                        cutout: '62%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { usePointStyle: true, pointStyle: 'rect', color: ink, font: { size: 12, weight: '600' }, padding: 16 },
                            },
                        },
                    },
                }),
            ];
        };
        if (window.Chart) {
            draw();
        } else {
            const tag = document.createElement('script');
            tag.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
            tag.onload = draw;
            document.head.appendChild(tag);
        }
    </script>
    @endscript
</x-filament-panels::page>
