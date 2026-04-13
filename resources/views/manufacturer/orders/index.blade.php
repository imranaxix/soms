@extends('layouts.app')

@section('title', 'Incoming Orders - Manufacturer')
@section('page_title', 'Incoming Orders')
@section('page_subtitle', 'Manage and track all your received orders')

@section('content')
<div class="space-y-6">

    <!-- Filter & Sort Section -->
    <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-neutral-700 mb-2">Filter by Status</label>
                <select class="w-full bg-neutral-50 border border-neutral-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                    <option>All Statuses</option>
                    <option>Pending</option>
                    <option>In Progress</option>
                    <option>Completed</option>
                    <option>Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-neutral-700 mb-2">Sort By</label>
                <select class="w-full bg-neutral-50 border border-neutral-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                    <option>Newest First</option>
                    <option>Oldest First</option>
                    <option>Amount: High to Low</option>
                    <option>Amount: Low to High</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-primary-600 text-white">
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Product</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Shop Owner</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-neutral-900">{{ $order['order_id'] }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-neutral-900">{{ $order['product'] }}</div>
                                <div class="text-[10px] text-neutral-500">Qty: {{ $order['qty'] }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-600">{{ $order['shop_owner'] }}</td>
                            <td class="px-6 py-4 text-sm text-neutral-600">{{ $order['date'] }}</td>
                            <td class="px-6 py-4 font-semibold text-neutral-900">₹{{ number_format($order['amount']) }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusClasses = [
                                        'Pending' => 'bg-orange-100 text-orange-700',
                                        'In Progress' => 'bg-blue-100 text-blue-700',
                                        'Completed' => 'bg-green-100 text-green-700',
                                        'Rejected' => 'bg-red-100 text-red-700',
                                    ];
                                    $class = $statusClasses[$order['status']] ?? 'bg-neutral-100 text-neutral-700';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $class }}">
                                    {{ $order['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    @if($order['status'] == 'Pending')
                                        <button class="px-3 py-1.5 bg-success-600 text-white text-[10px] font-bold rounded shadow-sm hover:bg-success-700 transition-all uppercase">Accept</button>
                                        <button class="px-3 py-1.5 bg-error-500 text-white text-[10px] font-bold rounded shadow-sm hover:bg-error-600 transition-all uppercase">Reject</button>
                                    @else
                                        <button class="px-4 py-1.5 bg-primary-600 text-white text-[10px] font-bold rounded shadow-sm hover:bg-primary-700 transition-all uppercase flex items-center gap-1">
                                            Track
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-neutral-500">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
