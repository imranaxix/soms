@extends('layouts.app')

@section('title', 'Reports & Analytics - SOMS')

@section('page_title', 'Reports & Analytics')
@section('page_subtitle', 'Detailed financial and order insights')

@section('header_actions')
    <div class="flex items-center gap-3">
        <button class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-neutral-200 text-neutral-700 rounded-lg text-sm font-bold hover:bg-neutral-50 transition shadow-sm">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 16V17C4 18.1046 4.89543 19 6 19H18C19.1046 19 20 18.1046 20 17V16M16 12L12 16M12 16L8 12M12 16V4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Export PDF
        </button>
        <button class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-neutral-200 text-neutral-700 rounded-lg text-sm font-bold hover:bg-neutral-50 transition shadow-sm">
            March 01 - March 18, 2026
        </button>
    </div>
@endsection

@section('content')
<div class="max-w-7xl  mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8 animate-fade-in">
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Spend -->
        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-100 text-white">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8Z" stroke="currentColor" stroke-width="2.5" />
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Total Spend</p>
                <p class="text-2xl font-black text-neutral-900 mt-1">₹{{ number_format($stats['totalSpend']) }}</p>
            </div>
        </div>

        <!-- Pending Liabilities -->
        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-orange-500 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-100 text-white">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="2.5" />
                    <path d="M12 7V12L15 15" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Pending Liabilities</p>
                <p class="text-2xl font-black text-neutral-900 mt-1">₹{{ number_format($stats['pendingLiabilities']) }}</p>
            </div>
        </div>

        <!-- Orders Fulfilled -->
        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-green-500 rounded-2xl flex items-center justify-center shadow-lg shadow-green-100 text-white">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Orders Fulfilled</p>
                <p class="text-2xl font-black text-neutral-900 mt-1">{{ $stats['ordersFulfilled'] }}</p>
            </div>
        </div>

        <!-- Top Manufacturer -->
        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-cyan-500 rounded-2xl flex items-center justify-center shadow-lg shadow-cyan-100 text-white">
                <span class="font-bold text-xl">{{ substr($stats['topManufacturer'], 0, 1) }}</span>
            </div>
            <div>
                <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Top Manufacturer</p>
                <p class="text-lg font-black text-neutral-900 mt-1 leading-tight">{{ $stats['topManufacturer'] }}</p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Spending Trends -->
        <div class="bg-white p-8 rounded-2xl border border-neutral-100 shadow-sm">
            <h3 class="text-lg font-bold text-neutral-900 mb-8">Spending Trends</h3>
            <div class="h-[300px] w-full">
                <canvas id="spendingChart"></canvas>
            </div>
        </div>

        <!-- Order Distribution -->
        <div class="bg-white p-8 rounded-2xl border border-neutral-100 shadow-sm">
            <h3 class="text-lg font-bold text-neutral-900 mb-8">Order Status Distribution</h3>
            <div class="h-[300px] w-full flex items-center justify-center">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-3xl border border-neutral-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-neutral-100">
            <h3 class="text-lg font-bold text-neutral-900">Recent Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-blue-600 text-white text-[11px] font-bold uppercase tracking-widest">
                        <th class="px-8 py-5">Transaction ID</th>
                        <th class="px-8 py-5">Date</th>
                        <th class="px-8 py-5">Manufacturer</th>
                        <th class="px-8 py-5">Method</th>
                        <th class="px-8 py-5">Status</th>
                        <th class="px-8 py-5 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-50">
                    @foreach($transactions as $trx)
                    <tr class="hover:bg-neutral-50/50 transition-colors">
                        <td class="px-8 py-5 text-sm font-bold text-neutral-600 font-mono">{{ $trx['id'] }}</td>
                        <td class="px-8 py-5 text-sm text-neutral-500">{{ $trx['date'] }}</td>
                        <td class="px-8 py-5 text-sm font-bold text-neutral-800">{{ $trx['manufacturer'] }}</td>
                        <td class="px-8 py-5 text-sm text-neutral-500">{{ $trx['method'] }}</td>
                        <td class="px-8 py-5">
                            <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                {{ $trx['status'] == 'Paid' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $trx['status'] == 'Pending' ? 'bg-orange-100 text-orange-700' : '' }}
                                {{ $trx['status'] == 'Overdue' ? 'bg-red-100 text-red-700' : '' }}
                            ">
                                {{ $trx['status'] }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-sm font-black text-neutral-900 text-right">₹{{ number_format($trx['amount']) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Spending Trends Chart
    const ctxSpending = document.getElementById('spendingChart').getContext('2d');
    new Chart(ctxSpending, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['spending']['labels']) !!},
            datasets: [{
                label: 'Total Spending',
                data: {!! json_encode($chartData['spending']['data']) !!},
                backgroundColor: '#f87171', // Red color matching the image
                borderRadius: 4,
                barThickness: 40,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        font: { size: 12, weight: 'bold' }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6', drawBorder: false },
                    ticks: {
                        font: { size: 10 },
                        callback: function(value) { return value.toLocaleString(); }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });

    // Order Distribution Chart
    const ctxDist = document.getElementById('distributionChart').getContext('2d');
    new Chart(ctxDist, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($chartData['distribution']['labels']) !!},
            datasets: [{
                data: {!! json_encode($chartData['distribution']['data']) !!},
                backgroundColor: [
                    '#fb923c', // Pending (Orange)
                    '#3b82f6', // In Progress (Blue)
                    '#22c55e', // Completed (Green)
                    '#ef4444'  // Rejected (Red)
                ],
                borderWidth: 0,
                weight: 0.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        font: { size: 12, weight: 'bold' }
                    }
                }
            }
        }
    });
});
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.5s ease-out forwards;
}
</style>

@endsection
