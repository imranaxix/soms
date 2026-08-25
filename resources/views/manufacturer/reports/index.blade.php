@extends('layouts.app')

@section('title', 'Reports & Analytics - Manufacturer')
@section('page_title', 'Reports & Analytics')
@section('page_subtitle', 'Detailed financial and order insights')

@section('header_actions')
    <div class="flex items-center gap-3">
        <form method="GET" action="{{ route('manufacturer.reports.index') }}">
            <select name="period" onchange="this.form.submit()" class="bg-white border border-neutral-200 rounded-lg px-4 py-1.5 text-sm font-bold text-neutral-600 focus:outline-none focus:ring-2 focus:ring-primary-500 shadow-sm cursor-pointer uppercase tracking-wider">
                <option value="all" {{ ($period ?? 'all') === 'all' ? 'selected' : '' }}>All Time</option>
                <option value="30days" {{ ($period ?? '') === '30days' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="this_month" {{ ($period ?? '') === 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="this_year" {{ ($period ?? '') === 'this_year' ? 'selected' : '' }}>This Year</option>
            </select>
        </form>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-1.5 bg-white border border-neutral-200 rounded-lg text-sm font-bold text-neutral-600 hover:bg-neutral-50 transition-colors shadow-sm uppercase tracking-wider">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 15V19C21 19.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 19V15M7 10L12 15M12 15L17 10M12 15V3" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Export PDF
        </button>
    </div>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Analytics KPI Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shrink-0">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div>
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Total Revenue</p>
                <h3 class="text-xl font-black text-neutral-900">Rs {{ number_format($stats['totalRevenue']) }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-orange-50 text-orange-500 rounded-xl flex items-center justify-center shrink-0">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Pending Receivables</p>
                <h3 class="text-xl font-black text-neutral-900">Rs {{ number_format($stats['pendingReceivables']) }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center shrink-0">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8"><path d="M20 6L9 17L4 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div>
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Orders Fulfilled</p>
                <h3 class="text-xl font-black text-neutral-900">{{ $stats['ordersFulfilled'] }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-teal-600 text-white rounded-xl flex items-center justify-center text-lg font-black shrink-0">
                {{ strtoupper(substr($stats['topCustomer'], 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Top Customer</p>
                <h3 class="text-lg font-black text-neutral-900 truncate">{{ $stats['topCustomer'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Revenue Trends (real Chart.js bar chart) -->
        <div class="bg-white p-8 rounded-2xl border border-neutral-100 shadow-sm">
            <h2 class="text-base font-black text-neutral-900 mb-6">Revenue Trends <span class="text-xs font-medium text-neutral-400 ml-1">(last 6 months)</span></h2>
            <div class="relative h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Order Status Distribution (real Chart.js doughnut chart) -->
        <div class="bg-white p-8 rounded-2xl border border-neutral-100 shadow-sm">
            <h2 class="text-base font-black text-neutral-900 mb-6">Order Status Distribution</h2>
            <div class="flex items-center gap-8 h-64">
                <div class="relative flex-1 flex items-center justify-center h-full">
                    <canvas id="donutChart"></canvas>
                </div>
                <div class="flex flex-col gap-3 shrink-0">
                    @php
                        $segments = [
                            ['label' => 'Pending',     'color' => 'bg-orange-400',  'value' => $chartData['distribution']['data'][0]],
                            ['label' => 'In Progress', 'color' => 'bg-blue-500',    'value' => $chartData['distribution']['data'][1]],
                            ['label' => 'Completed',   'color' => 'bg-green-500',   'value' => $chartData['distribution']['data'][2]],
                            ['label' => 'Rejected',    'color' => 'bg-red-500',     'value' => $chartData['distribution']['data'][3]],
                        ];
                    @endphp
                    @foreach($segments as $s)
                    <div class="flex items-center gap-2.5">
                        <div class="w-3 h-3 {{ $s['color'] }} rounded-full shrink-0"></div>
                        <div>
                            <span class="text-[11px] text-neutral-700 font-bold uppercase tracking-wider">{{ $s['label'] }}</span>
                            <span class="text-[11px] text-neutral-400 font-bold ml-1.5">{{ $s['value'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-neutral-50 flex items-center justify-between">
            <h2 class="text-sm font-black text-neutral-700 uppercase tracking-widest">Recent Transactions</h2>
            <span class="text-xs font-bold text-neutral-400">Last 10 payments received</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-primary-600 text-white">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Transaction ID</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Partner</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Method</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-neutral-800 font-mono">{{ $trx['id'] }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-neutral-500">{{ $trx['date'] }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-neutral-800">{{ $trx['partner'] }}</td>
                        <td class="px-6 py-4 text-xs text-neutral-500 font-bold">{{ $trx['method'] }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 uppercase tracking-tighter">
                                {{ $trx['status'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-black text-neutral-900">Rs {{ number_format($trx['amount']) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-neutral-400 font-bold text-sm">No transactions yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(function () {
    // ── Revenue Bar Chart ──────────────────────────────────────
    const revenueLabels = @json($chartData['revenue']['labels']);
    const revenueData   = @json($chartData['revenue']['data']);

    const revenueCtx = document.getElementById('revenueChart').getContext('2d');

    const gradient = revenueCtx.createLinearGradient(0, 0, 0, 256);
    gradient.addColorStop(0, 'rgba(37,99,235,0.85)');
    gradient.addColorStop(1, 'rgba(99,102,241,0.4)');

    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Revenue (Rs)',
                data: revenueData,
                backgroundColor: gradient,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' Rs ' + ctx.parsed.y.toLocaleString()
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11, weight: 'bold' }, color: '#9ca3af' }
                },
                y: {
                    beginAtZero: true,
                    border: { dash: [4,4] },
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        font: { size: 11, weight: 'bold' },
                        color: '#9ca3af',
                        callback: v => 'Rs ' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v)
                    }
                }
            }
        }
    });

    // ── Donut Chart ───────────────────────────────────────────
    const donutCtx = document.getElementById('donutChart').getContext('2d');
    const donutData = @json($chartData['distribution']['data']);
    const donutLabels = @json($chartData['distribution']['labels']);
    const total = donutData.reduce((a, b) => a + b, 0);

    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: donutLabels,
            datasets: [{
                data: total > 0 ? donutData : [1],
                backgroundColor: total > 0
                    ? ['#fb923c', '#3b82f6', '#22c55e', '#ef4444']
                    : ['#e5e7eb'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: total > 0,
                    callbacks: {
                        label: ctx => ' ' + ctx.label + ': ' + ctx.parsed + ' orders'
                    }
                }
            }
        }
    });
})();
</script>
@endsection
