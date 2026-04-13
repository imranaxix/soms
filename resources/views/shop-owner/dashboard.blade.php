@extends('layouts.app')

@section('title', 'Shop Owner Dashboard - SOMS')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Overview of your orders and payments')


@section('header_actions')
    <a href="{{ route('shop.orders.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors shadow-sm">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Create New Order
    </a>
@endsection

@section('content')
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 mt-4">
        <div class="bg-white rounded-xl p-6 flex items-center gap-4 shadow-sm border border-neutral-100">
            <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 11H5M19 11C20.1046 11 21 11.8954 21 13V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V13C3 11.8954 3.89543 11 5 11M19 11V9C19 7.89543 18.1046 7 17 7M5 11V9C5 7.89543 5.89543 7 7 7M17 7V5C17 3.89543 16.1046 3 15 3H9C7.89543 3 7 3.89543 7 5V7M17 7H7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-neutral-500">Total Orders</p>
                <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 flex items-center gap-4 shadow-sm border border-neutral-100">
            <div class="w-12 h-12 bg-warning-100 text-warning-600 rounded-lg flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8V12L15 15M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-neutral-500">Pending</p>
                <p class="text-2xl font-bold">{{ $stats['pending'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 flex items-center gap-4 shadow-sm border border-neutral-100">
            <div class="w-12 h-12 bg-[#e0f2fe] text-[#0284c7] rounded-lg flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 4V20M20 12H4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-neutral-500">In Progress</p>
                <p class="text-2xl font-bold">{{ $stats['inProgress'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 flex items-center gap-4 shadow-sm border border-neutral-100">
            <div class="w-12 h-12 bg-success-100 text-success-600 rounded-lg flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-neutral-500">Completed</p>
                <p class="text-2xl font-bold">{{ $stats['completed'] }}</p>
            </div>
        </div>
    </div>

    <!-- Payment Summary -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-neutral-100 mb-8 mt-2">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-neutral-900 leading-none">Payment Summary</h2>
        </div>
        <div class="flex items-center justify-between px-16 py-6">
            <div class="flex flex-col justify-center items-center">
                <p class="text-sm text-neutral-500 uppercase tracking-wider mb-1">Total Amount</p>
                <p class="text-2xl font-bold text-neutral-900">₹{{ number_format($stats['totalAmount']) }}</p>
            </div>
            <div class="flex flex-col justify-center items-center">
                <p class="text-sm text-neutral-500 uppercase tracking-wider mb-1">Paid</p>
                <p class="text-2xl font-bold text-success-600">₹{{ number_format($stats['totalPaid']) }}</p>
            </div>
            <div class="flex flex-col justify-center items-center">
                <p class="text-sm text-neutral-500 uppercase tracking-wider mb-1">Pending</p>
                <p class="text-2xl font-bold text-warning-600">₹{{ number_format($stats['totalPending']) }}</p>
            </div>
        </div>
        <div class="mt-8 flex flex-col justify-center items-center">
            <div class="w-full bg-neutral-100 h-2.5 rounded-full overflow-hidden">
                <div class="bg-primary-500 h-full transition-all duration-500" style="width: {{ $stats['totalAmount'] > 0 ? ($stats['totalPaid'] / $stats['totalAmount']) * 100 : 0 }}%"></div>
            </div>
            <p class="text-sm text-neutral-400 mt-4">
                {{ $stats['totalAmount'] > 0 ? round(($stats['totalPaid'] / $stats['totalAmount']) * 100) : 0 }}% Paid
            </p>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-neutral-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-neutral-900">Recent Orders</h2>
            <a href="{{ route('shop.orders.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">View All</a>
        </div>

        @if(count($orders) === 0)
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-neutral-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" class="text-neutral-300" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 11H5M19 11C20.1046 11 21 11.8954 21 13V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V13C3 11.8954 3.89543 11 5 11M19 11V9C19 7.89543 18.1046 7 17 7M5 11V9C5 7.89543 5.89543 7 7 7M17 7V5C17 3.89543 16.1046 3 15 3H9C7.89543 3 7 3.89543 7 5V7M17 7H7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-neutral-800">No orders yet</h3>
                <p class="text-sm text-neutral-500 mb-6 mt-1">Create your first order to get started</p>
                <a href="{{ route('shop.orders.create') }}" class="inline-flex py-2 px-4 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700">Create Order</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class=" bg-primary-600 border-b border-neutral-100">
                            <th class="px-6 py-4 text-[13px] font-medium text-primary-50 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-4 text-[13px] font-medium text-primary-50 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-4 text-[13px] font-medium text-primary-50 uppercase tracking-wider">Manufacturer</th>
                            <th class="px-6 py-4 text-[13px] font-medium text-primary-50 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-4 text-[13px] font-medium text-primary-50 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-4 text-[13px] font-medium text-primary-50 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-[13px] font-medium text-primary-50 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-4 text-[13px] font-medium text-primary-50 uppercase tracking-wider">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($orders as $order)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-semibold text-primary-600">#{{ substr($order['id'], -6) }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-neutral-800">{{ $order['productName'] }}</td>
                                <td class="px-6 py-4 text-sm text-neutral-600">{{ $order['manufacturerName'] }}</td>
                                <td class="px-6 py-4 text-sm text-neutral-600">{{ $order['quantity'] }}</td>
                                <td class="px-6 py-4 text-sm text-neutral-600">{{ date('M d, Y', strtotime($order['dueDate'])) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-tight 
                                        {{ $order['status'] == 'Pending' ? 'bg-warning-100 text-warning-600' : ($order['status'] == 'In Progress' ? 'bg-primary-100 text-primary-600' : 'bg-success-100 text-success-600') }}">
                                        {{ $order['status'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-neutral-800">₹{{ number_format($order['totalAmount']) }}</td>
                                <td class="px-6 py-4">
                                    <div class="w-32 flex justify-center items-center">
                                        <div class="w-full bg-neutral-100 h-1.5 rounded-full overflow-hidden mb-1 mr-4">
                                            <div class="bg-primary-500 h-full" style="width: {{ $order['productionProgress'] }}%"></div>
                                        </div>
                                        <span class="text-[10px] text-neutral-400 font-medium">{{ $order['productionProgress'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
