@extends('layouts.app')

@section('title', 'Manufacturer Dashboard - SOMS')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Manage incoming orders and production')

@section('content')
<div class="space-y-6">
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
                    @foreach($pendingOrders as $order)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-neutral-900">{{ $order['order_id'] }}</td>
                        <td class="px-6 py-4 text-neutral-600">{{ $order['shop_owner'] }}</td>
                        <td class="px-6 py-4 text-neutral-600">{{ $order['product'] }}</td>
                        <td class="px-6 py-4 text-neutral-600">{{ $order['quantity'] }}</td>
                        <td class="px-6 py-4 text-neutral-600 text-sm italic">{{ $order['due_date'] }}</td>
                        <td class="px-6 py-4 font-semibold text-neutral-900">₹{{ number_format($order['amount']) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button class="px-3 py-1.5 bg-success-600 text-white text-xs font-bold rounded-md hover:bg-success-700 transition-colors uppercase">Accept</button>
                                <button class="px-3 py-1.5 bg-error-500 text-white text-xs font-bold rounded-md hover:bg-error-600 transition-colors uppercase">Reject</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payment Summary Section -->
    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-neutral-900 mb-6">Payment Summary</h2>
        <div class="grid grid-cols-3 gap-8 text-center mb-8">
            <div>
                <p class="text-xs text-neutral-500 uppercase font-bold tracking-wider mb-2">Total Revenue</p>
                <h3 class="text-2xl font-bold text-neutral-900">₹{{ number_format($stats['totalRevenue']) }}</h3>
            </div>
            <div>
                <p class="text-xs text-neutral-500 uppercase font-bold tracking-wider mb-2">Received</p>
                <h3 class="text-2xl font-bold text-success-600">₹{{ number_format($stats['receivedPayment']) }}</h3>
            </div>
            <div>
                <p class="text-xs text-neutral-500 uppercase font-bold tracking-wider mb-2">Pending</p>
                <h3 class="text-2xl font-bold text-orange-600">₹{{ number_format($stats['pendingPayment']) }}</h3>
            </div>
        </div>
        <div class="relative w-full h-2 bg-neutral-100 rounded-full overflow-hidden">
            <div class="absolute inset-y-0 left-0 bg-primary-500 rounded-full transition-all duration-1000" style="width: 15%"></div>
        </div>
        <p class="text-center text-xs text-neutral-500 mt-3 font-semibold">15% Received</p>
    </div>

    <!-- Active Orders Section -->
    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-neutral-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-neutral-900">Active Orders</h2>
            <button class="text-xs font-bold text-blue-600 hover:text-blue-700 uppercase">View All</button>
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
                    @foreach($activeOrders as $order)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-neutral-900">{{ $order['order_id'] }}</td>
                        <td class="px-6 py-4 text-neutral-600">{{ $order['shop_owner'] }}</td>
                        <td class="px-6 py-4 text-neutral-600">{{ $order['product'] }}</td>
                        <td class="px-6 py-4 text-neutral-600">{{ $order['quantity'] }}</td>
                        <td class="px-6 py-4 text-neutral-600 text-sm italic">{{ $order['due_date'] }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-1.5 bg-neutral-100 rounded-full overflow-hidden min-w-[60px]">
                                    <div class="h-full bg-primary-500" style="width: {{ $order['progress'] }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-neutral-500">{{ $order['progress'] }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-neutral-900">₹{{ number_format($order['amount']) }}</td>
                        <td class="px-6 py-4 text-right">
                            <button class="px-3 py-1.5 bg-success-600/50 text-white text-xs font-bold rounded-md cursor-not-allowed uppercase">Complete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Spending Trends (Mock Chart) -->
        <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm">
            <h2 class="text-lg font-bold text-neutral-900 mb-6">Spending Trends</h2>
            <div class="h-64 flex items-end gap-4 px-4 border-b border-l border-neutral-100 pb-2 relative">
                @php $months = ['Oct 2025', 'Nov 2025', 'Dec 2025', 'Jan 2026', 'Feb 2026', 'Mar 2026']; @endphp
                @foreach($months as $index => $month)
                <div class="flex-1 flex flex-col items-center gap-2 group">
                    <div class="w-full bg-error-400/50 group-hover:bg-error-400 transition-colors rounded-t-sm" 
                         style="height: {{ $index == 5 ? '80%' : '2%' }}"></div>
                    <span class="text-[10px] text-neutral-400 whitespace-nowrap">{{ $month }}</span>
                </div>
                @endforeach
                
                <!-- Legend -->
                <div class="absolute bottom-[-40px] left-1/2 transform -translate-x-1/2 flex items-center gap-2">
                    <div class="w-3 h-3 bg-error-400 rounded-full"></div>
                    <span class="text-xs font-bold text-neutral-600">Total Spending</span>
                </div>
            </div>
        </div>

        <!-- Order Status Distribution (Mock Chart) -->
        <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm">
            <h2 class="text-lg font-bold text-neutral-900 mb-6">Order Status Distribution</h2>
            <div class="h-64 flex flex-col items-center justify-center relative">
                <!-- Donut Chart Circle -->
                <div class="w-48 h-48 rounded-full border-[18px] border-neutral-100 flex items-center justify-center relative">
                    <!-- Segment Blue -->
                    <div class="absolute inset-0 rounded-full border-[18px] border-transparent border-t-orange-500 border-r-orange-500 rotate-[-45deg]"></div>
                    <!-- Segment Green -->
                    <div class="absolute inset-0 rounded-full border-[18px] border-transparent border-l-primary-500 border-b-primary-500 rotate-[30deg]"></div>
                </div>
                
                <!-- Legend Beside -->
                <div class="absolute right-0 top-1/2 -translate-y-1/2 flex flex-col gap-3">
                    @php
                        $segments = [
                            ['label' => 'Pending', 'color' => 'bg-orange-500'],
                            ['label' => 'In Progress', 'color' => 'bg-primary-500'],
                            ['label' => 'Completed', 'color' => 'bg-success-500'],
                            ['label' => 'Rejected', 'color' => 'bg-error-500'],
                        ];
                    @endphp
                    @foreach($segments as $s)
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 {{ $s['color'] }} rounded-full"></div>
                        <span class="text-xs text-neutral-600 font-medium">{{ $s['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
