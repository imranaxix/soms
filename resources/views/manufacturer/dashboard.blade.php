@extends('layouts.app')

@section('title', 'Manufacturer Dashboard - SOMS')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Manage incoming orders and production')

@section('content')
<div class="space-y-6">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-5 py-4 bg-success-50 border border-success-200 text-success-700 rounded-xl text-sm font-medium">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 13.01 9 10.01"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-5 py-4 bg-error-50 border border-error-200 text-error-700 rounded-xl text-sm font-medium">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif
    <!-- KPI Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Orders -->
        <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 11H5M19 11C20.1046 11 21 11.8954 21 13V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V13C3 11.8954 3.89543 11 5 11M19 11V9C19 7.89543 18.1046 7 17 7M5 11V9C5 7.89543 5.89543 7 7 7M17 7V5C17 3.89543 16.1046 3 15 3H9C7.89543 3 7 3.89543 7 5V7M17 7H7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-500 font-medium">Total Orders</p>
                <h3 class="text-2xl font-bold">{{ $stats['totalOrders'] }}</h3>
            </div>
        </div>

        <!-- Pending Approval -->
        <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center text-orange-600">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8V12L15 15M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-500 font-medium">Pending Approval</p>
                <h3 class="text-2xl font-bold">{{ $stats['pendingApproval'] }}</h3>
            </div>
        </div>

        <!-- In Production -->
        <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-teal-50 rounded-lg flex items-center justify-center text-teal-600">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 4V20M20 12H4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-500 font-medium">In Production</p>
                <h3 class="text-2xl font-bold">{{ $stats['inProduction'] }}</h3>
            </div>
        </div>

        <!-- Completed -->
        <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center text-green-600">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-neutral-500 font-medium">Completed</p>
                <h3 class="text-2xl font-bold">{{ $stats['completed'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Pending Orders Section -->
    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-neutral-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-neutral-900">Pending Orders - Action Required</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 mt-1">
                    {{ count($pendingOrders) }} Pending
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-primary-600 text-white">
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Shop Owner</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Product</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($pendingOrders as $order)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-neutral-900">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 text-neutral-600">{{ $order->shopOwner->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-neutral-600">{{ $order->product->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-neutral-600">{{ $order->quantity }} {{ $order->unit }}</td>
                        <td class="px-6 py-4 text-neutral-600 text-sm italic">{{ $order->due_date ? $order->due_date->format('M d, Y') : 'No date' }}</td>
                        <td class="px-6 py-4 font-semibold text-neutral-900">Rs {{ number_format($order->total_amount) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('manufacturer.orders.show', $order->id) }}"
                                   class="px-3 py-1.5 bg-primary-600 text-white text-xs font-bold rounded-md hover:bg-primary-700 transition-colors uppercase">View</a>

                                <form action="{{ route('manufacturer.orders.accept', $order->id) }}" method="POST"
                                      onsubmit="return confirm('Accept order {{ $order->order_number }}?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-success-600 text-white text-xs font-bold rounded-md hover:bg-success-700 transition-colors uppercase">Accept</button>
                                </form>

                                <form action="{{ route('manufacturer.orders.reject', $order->id) }}" method="POST"
                                      onsubmit="return confirm('Reject order {{ $order->order_number }}?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-error-500 text-white text-xs font-bold rounded-md hover:bg-error-600 transition-colors uppercase">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-neutral-500 italic">No pending orders at the moment.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payment Summary Section -->
    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-neutral-900 mb-6">Payment Summary</h2>
        <div class="grid grid-cols-3 gap-8 text-center mb-8 py-6">
            <div>
                <p class="text-xs text-neutral-500 uppercase font-bold tracking-wider mb-2">Total Revenue</p>
                <h3 class="text-2xl font-bold text-neutral-900">Rs {{ number_format($stats['totalRevenue']) }}</h3>
            </div>
            <div>
                <p class="text-xs text-neutral-500 uppercase font-bold tracking-wider mb-2">Received</p>
                <h3 class="text-2xl font-bold text-success-600">Rs {{ number_format($stats['receivedPayment']) }}</h3>
            </div>
            <div>
                <p class="text-xs text-neutral-500 uppercase font-bold tracking-wider mb-2">Pending</p>
                <h3 class="text-2xl font-bold text-orange-600">Rs {{ number_format($stats['pendingPayment']) }}</h3>
            </div>
        </div>
        @php
            $paymentProgress = $stats['totalRevenue'] > 0 ? ($stats['receivedPayment'] / $stats['totalRevenue']) * 100 : 0;
        @endphp
        <div class="relative w-full h-2 bg-neutral-100 rounded-full overflow-hidden">
            <div class="absolute inset-y-0 left-0 bg-primary-500 rounded-full transition-all duration-1000" style="width: {{ $paymentProgress }}%"></div>
        </div>
        <p class="text-center text-xs text-neutral-500 mt-3 font-semibold">{{ number_format($paymentProgress, 1) }}% Received</p>
    </div>

    <!-- Active Orders Section -->
    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-neutral-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-neutral-900">Active Orders</h2>
            <a href="{{ route('manufacturer.orders.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 uppercase">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-primary-600 text-white">
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Shop Owner</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Product</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($activeOrders as $order)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-neutral-900">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 text-neutral-600">{{ $order->shopOwner->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-neutral-600">{{ $order->product->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-neutral-600">{{ $order->quantity }} {{ $order->unit }}</td>
                        <td class="px-6 py-4 text-neutral-600 text-sm italic">{{ $order->due_date ? $order->due_date->format('M d, Y') : 'No date' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-1.5 bg-neutral-100 rounded-full overflow-hidden min-w-[60px]">
                                    <div class="h-full bg-primary-500" style="width: {{ $order->progress_percent }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-neutral-500">{{ $order->progress_percent }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-neutral-900">Rs {{ number_format($order->total_amount) }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('manufacturer.orders.show', $order->id) }}"
                               class="px-3 py-1.5 bg-primary-600 text-white text-xs font-bold rounded-md hover:bg-primary-700 transition-colors uppercase">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-neutral-500 italic">No active orders in production.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Spending Trends Chart -->
        <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm">
            <h2 class="text-lg font-bold text-neutral-900 mb-6">Spending Trends</h2>
            <canvas id="spendingChart"></canvas>
        </div>

        <!-- Order Status Distribution Chart -->
        <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm">
            <h2 class="text-lg font-bold text-neutral-900 mb-6">Order Status Distribution</h2>
            <canvas id="statusDonut"></canvas>
        </div>

        @php
            $distributionColors = $distributionTotal > 0
                ? ['#fb923c', '#3b82f6', '#22c55e', '#ef4444']
                : ['#e5e7eb'];
            $distributionHasData = $distributionTotal > 0;
        @endphp
        <script>
            // Spending Trends Bar Chart
            const spendingCtx = document.getElementById('spendingChart').getContext('2d');
            new Chart(spendingCtx, {
                type: 'bar',
                data: {
                    labels: @json($revenueLabels),
                    datasets: [{
                        label: 'Revenue (Rs)',
                        data: @json($revenueData),
                        backgroundColor: 'rgba(37,99,235,0.85)',
                        borderColor: 'rgba(37,99,235,1)',
                        borderWidth: 1,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ' Rs ' + ctx.parsed.y.toLocaleString() } } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11, weight: 'bold' }, color: '#9ca3af' } },
                        y: { beginAtZero: true, border: { dash: [4,4] }, grid: { color: '#f3f4f6' }, ticks: { font: { size: 11, weight: 'bold' }, color: '#9ca3af', callback: v => 'Rs ' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v) } }
                    }
                }
            });

            // Order Status Distribution Doughnut Chart
            const donutCtx = document.getElementById('statusDonut').getContext('2d');
            new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($distributionLabels),
                    datasets: [{
                        data: @json($distributionData),
                        backgroundColor: @json($distributionColors),
                        borderWidth: 0,
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: { legend: { display: false }, tooltip: { enabled: @json($distributionHasData), callbacks: { label: ctx => ' ' + ctx.label + ': ' + ctx.parsed + ' orders' } } }
                }
            });
        </script>
    </div>
</div>
@endsection
