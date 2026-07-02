@extends('layouts.app')

@section('title', 'Incoming Orders - Manufacturer')
@section('page_title', 'Incoming Orders')
@section('page_subtitle', 'Manage and track all your received orders')

@section('content')
<div class="space-y-6">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-5 py-4 bg-success-50 border border-success-200 text-success-700 rounded-xl text-sm font-medium">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 13.01 9 10.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Pending Orders Alert --}}
    @php $pendingCount = $orders->where('status', 'Pending')->count(); @endphp
    @if($pendingCount > 0)
        <div class="flex items-center gap-3 px-5 py-4 bg-warning-50 border border-warning-200 text-warning-700 rounded-xl text-sm font-medium">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            You have <strong>{{ $pendingCount }} pending {{ Str::plural('order', $pendingCount) }}</strong> awaiting your decision.
        </div>
    @endif

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
                            <td class="px-6 py-4 font-medium text-neutral-900">{{ $order->order_number }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-neutral-900">{{ $order->product->name ?? 'N/A' }}</div>
                                <div class="text-[10px] text-neutral-500">Qty: {{ $order->quantity }} {{ $order->unit }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-600">{{ $order->shopOwner->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4 text-sm text-neutral-600">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-semibold text-neutral-900">Rs {{ number_format($order->total_amount) }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusClasses = [
                                        'Pending'     => 'bg-orange-100 text-orange-700',
                                        'In Progress' => 'bg-blue-100 text-blue-700',
                                        'Delivered'   => 'bg-purple-100 text-purple-700',
                                        'Completed'   => 'bg-green-100 text-green-700',
                                        'Rejected'    => 'bg-red-100 text-red-700',
                                        'Cancelled'   => 'bg-neutral-200 text-neutral-700',
                                    ];
                                    $class = $statusClasses[$order->status] ?? 'bg-neutral-100 text-neutral-700';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $class }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- View Details — always shown --}}
                                    <a href="{{ route('manufacturer.orders.show', $order->id) }}"
                                       class="px-3 py-1.5 bg-primary-600 text-white text-[10px] font-bold rounded shadow-sm hover:bg-primary-700 transition-all uppercase">
                                        View
                                    </a>

                                    @if($order->status === 'Pending')
                                        {{-- Accept --}}
                                        <form action="{{ route('manufacturer.orders.accept', $order->id) }}" method="POST"
                                              onsubmit="return confirm('Accept order {{ $order->order_number }}?')">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1.5 bg-success-600 text-white text-[10px] font-bold rounded shadow-sm hover:bg-success-700 transition-all uppercase">
                                                Accept
                                            </button>
                                        </form>

                                        {{-- Reject --}}
                                        <form action="{{ route('manufacturer.orders.reject', $order->id) }}" method="POST"
                                              onsubmit="return confirm('Reject order {{ $order->order_number }}? This cannot be undone.')">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1.5 bg-error-500 text-white text-[10px] font-bold rounded shadow-sm hover:bg-error-600 transition-all uppercase">
                                                Reject
                                            </button>
                                        </form>
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
