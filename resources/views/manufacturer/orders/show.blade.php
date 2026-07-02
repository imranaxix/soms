@extends('layouts.app')

@section('title', 'Order ' . $order->order_number . ' - Manufacturer')
@section('back_link', route('manufacturer.orders.index'))
@section('page_title', 'Order ' . $order->order_number)
@section('page_subtitle', 'Received on ' . $order->created_at->format('M d, Y \a\t H:i'))

@section('content')
<div class="max-w-7xl mx-auto animate-fade-in">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-5 py-4 mb-6 bg-success-50 border border-success-200 text-success-700 rounded-xl text-sm font-medium">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 13.01 9 10.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ===== LEFT COLUMN (Main) ===== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Product Card --}}
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="flex flex-col md:flex-row">
                    {{-- Product Image --}}
                    <div class="w-full md:w-48 h-48 bg-neutral-50 flex items-center justify-center p-4 shrink-0">
                        @if($order->variant && $order->variant->image)
                            <img src="{{ asset('storage/' . $order->variant->image) }}"
                                 alt="{{ $order->product->name }}"
                                 class="w-full h-full object-cover rounded-xl shadow-sm">
                        @else
                            <div class="w-full h-full bg-primary-50 rounded-xl flex flex-col items-center justify-center text-primary-200">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                                    <line x1="12" y1="22.08" x2="12" y2="12"/>
                                </svg>
                                <span class="text-[10px] font-bold uppercase mt-2 tracking-widest">No Image</span>
                            </div>
                        @endif
                    </div>

                    {{-- Product Info --}}
                    <div class="flex-1 p-6 flex flex-col justify-center">
                        <p class="text-[10px] font-bold text-primary-600 uppercase tracking-widest mb-1">Product Order</p>
                        <h3 class="text-2xl font-black text-neutral-900 leading-tight">{{ $order->product->name }}</h3>
                        <div class="flex items-center gap-3 mt-2 flex-wrap">
                            <span class="px-2 py-0.5 bg-neutral-100 text-neutral-600 rounded text-[11px] font-bold">
                                {{ $order->variant->variant_name ?? 'Standard' }}
                            </span>
                            @if($order->variant && $order->variant->sku)
                                <span class="text-neutral-400 text-[11px]">SKU: {{ $order->variant->sku }}</span>
                            @endif
                        </div>
                        <div class="mt-4 flex items-center gap-6">
                            <div>
                                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Quantity</p>
                                <p class="text-lg font-bold text-neutral-900">{{ number_format($order->quantity) }} {{ $order->unit }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Unit Price</p>
                                <p class="text-lg font-bold text-neutral-900">
                                    Rs {{ $order->quantity > 0 ? number_format($order->total_amount / $order->quantity, 0) : '—' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Total Value</p>
                                <p class="text-lg font-black text-primary-600">Rs {{ number_format($order->total_amount) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Banner: Cancel In-Progress Order --}}
            @if(in_array($order->status, ['In Progress', 'Delivered']))
            <div class="rounded-2xl border border-orange-200 bg-orange-50 p-5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-orange-500 shrink-0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-orange-800 text-sm">Need to cancel this order?</p>
                        <p class="text-xs text-orange-600 mt-0.5">This will notify the shop owner and reset all production progress.</p>
                    </div>
                </div>
                <form action="{{ route('manufacturer.orders.cancel', $order->id) }}" method="POST"
                      onsubmit="return confirm('Cancel order {{ $order->order_number }}? This will notify the shop owner and cannot be undone.');">
                    @csrf
                    <button type="submit" class="shrink-0 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl transition shadow-sm shadow-orange-200 text-sm whitespace-nowrap">
                        Cancel Order
                    </button>
                </form>
            </div>
            @endif

            {{-- Order Details --}}
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h2 class="text-base font-bold text-neutral-900">Order Details</h2>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Payment Terms</p>
                        <p class="text-sm font-semibold text-neutral-800">{{ str_replace('_', ' ', $order->payment_terms) }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Due Date</p>
                        <p class="text-sm font-semibold text-neutral-800">{{ $order->due_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Order Placed</p>
                        <p class="text-sm font-semibold text-neutral-800">{{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Current Status</p>
                        @php
                            $statusClasses = [
                                'Pending'     => 'bg-orange-100 text-orange-700',
                                'In Progress' => 'bg-blue-100 text-blue-700',
                                'Completed'   => 'bg-green-100 text-green-700',
                                'Rejected'    => 'bg-red-100 text-red-700',
                            ];
                            $sc = $statusClasses[$order->status] ?? 'bg-neutral-100 text-neutral-700';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $sc }}">
                            {{ $order->status }}
                        </span>
                    </div>

                    @if($order->special_instructions)
                        <div class="sm:col-span-2">
                            <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Special Instructions</p>
                            <p class="text-sm text-neutral-700 bg-neutral-50 rounded-lg p-3 border border-neutral-100">
                                {{ $order->special_instructions }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Order Timeline --}}
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h2 class="text-base font-bold text-neutral-900">Order Timeline</h2>
                </div>
                <div class="p-6">
                    <div class="relative">
                        <div class="absolute left-2.75 top-2 bottom-2 w-0.5 bg-neutral-100"></div>
                        <div class="space-y-8">

                            {{-- Step 1: Order Placed --}}
                            <div class="relative flex items-start gap-5">
                                <div class="mt-1 z-10">
                                    <div class="w-6 h-6 rounded-full bg-green-500 border-4 border-white shadow-sm flex items-center justify-center">
                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                    </div>
                                </div>
                                <div class="flex-1 flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold text-sm {{ $order->status === 'Cancelled' ? 'text-red-600' : 'text-neutral-900' }}">
                                            {{ $order->status === 'Cancelled' ? 'Order Cancelled' : 'Order Placed' }}
                                        </h4>
                                        <p class="text-xs text-neutral-500 mt-0.5">
                                            {{ $order->status === 'Cancelled' ? 'Shop owner cancelled the order' : 'Shop owner submitted the order request' }}
                                        </p>
                                    </div>
                                    <span class="text-xs font-medium text-neutral-400 whitespace-nowrap">{{ $order->created_at->format('M d, H:i') }}</span>
                                </div>
                            </div>

                            {{-- Step 2: Manufacturer Decision --}}
                            <div class="relative flex items-start gap-5">
                                <div class="mt-1 z-10">
                                    @if($order->status === 'In Progress' || $order->status === 'Completed' || $order->status === 'Delivered')
                                        <div class="w-6 h-6 rounded-full bg-green-500 border-4 border-white shadow-sm flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        </div>
                                    @elseif($order->status === 'Rejected')
                                        <div class="w-6 h-6 rounded-full bg-red-500 border-4 border-white shadow-sm flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-white border-4 border-neutral-200 shadow-sm"></div>
                                    @endif
                                </div>
                                <div class="flex-1 flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold text-sm {{ $order->status === 'Pending' || $order->status === 'Cancelled' ? 'text-neutral-400' : 'text-neutral-900' }}">
                                            @if($order->status === 'Rejected') Order Rejected
                                            @elseif($order->status === 'Pending') Awaiting Your Decision
                                            @elseif($order->status === 'Cancelled') Cancelled (No Decision Needed)
                                            @else Order Accepted
                                            @endif
                                        </h4>
                                        <p class="text-xs text-neutral-500 mt-0.5">
                                            @if($order->status === 'Pending') No action taken yet
                                            @elseif($order->status === 'Rejected') You rejected this order
                                            @elseif($order->status === 'Cancelled') Shop owner cancelled before you decided
                                            @else You accepted this order — production started
                                            @endif
                                        </p>
                                    </div>
                                    <span class="text-xs font-medium text-neutral-400">
                                        {{ $order->updated_at && $order->status !== 'Pending' ? $order->updated_at->format('M d, H:i') : '--' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Step 3: In Production --}}
                            <div class="relative flex items-start gap-5">
                                <div class="mt-1 z-10">
                                    @if($order->status === 'Completed' || $order->status === 'Delivered')
                                        <div class="w-6 h-6 rounded-full bg-green-500 border-4 border-white shadow-sm flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-white border-4 border-neutral-200 shadow-sm"></div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-sm {{ $order->status === 'In Progress' ? 'text-neutral-900' : 'text-neutral-400' }}">In Production</h4>
                                    <p class="text-xs text-neutral-500 mt-0.5">Items are being manufactured</p>
                                </div>
                            </div>

                            {{-- Step 4: Delivered --}}
                            <div class="relative flex items-start gap-5">
                                <div class="mt-1 z-10">
                                    @if($order->status === 'Completed')
                                        <div class="w-6 h-6 rounded-full bg-green-500 border-4 border-white shadow-sm flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        </div>
                                    @elseif($order->status === 'Delivered')
                                        <div class="w-6 h-6 rounded-full bg-white border-4 border-purple-500 shadow-sm"></div>
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-white border-4 border-neutral-200 shadow-sm"></div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-sm {{ $order->status === 'Delivered' ? 'text-purple-600' : 'text-neutral-400' }}">Delivered</h4>
                                    <p class="text-xs text-neutral-500 mt-0.5">Waiting for shop owner to confirm receipt</p>
                                </div>
                            </div>

                            {{-- Step 5: Completed --}}
                            <div class="relative flex items-start gap-5">
                                <div class="mt-1 z-10">
                                    @if($order->status === 'Completed')
                                        <div class="w-6 h-6 rounded-full bg-green-500 border-4 border-white shadow-sm flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-white border-4 border-neutral-200 shadow-sm"></div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-sm {{ $order->status === 'Completed' ? 'text-green-600' : 'text-neutral-400' }}">Order Completed</h4>
                                    <p class="text-xs text-neutral-500 mt-0.5">Shop owner confirmed receipt</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== RIGHT COLUMN (Sidebar) ===== --}}
        <div class="space-y-6">

            {{-- Accept / Reject Actions (only when Pending) --}}
            @if($order->status === 'Pending')
                <div class="bg-white rounded-2xl border border-orange-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-orange-50 border-b border-orange-100 flex items-center gap-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <h2 class="text-sm font-bold text-orange-700">Action Required</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <p class="text-xs text-neutral-500 mb-4">Review the order details and accept or reject this request.</p>

                        {{-- Accept --}}
                        <form action="{{ route('manufacturer.orders.accept', $order->id) }}" method="POST"
                              onsubmit="return confirm('Accept this order? Status will change to In Progress.')">
                            @csrf
                            <button type="submit"
                                class="w-full py-3 bg-success-600 hover:bg-success-700 text-white font-bold rounded-xl text-sm transition shadow-md shadow-success-200 flex items-center justify-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                Accept Order
                            </button>
                        </form>

                        {{-- Reject --}}
                        <form action="{{ route('manufacturer.orders.reject', $order->id) }}" method="POST"
                              onsubmit="return confirm('Reject this order? This action cannot be undone.')">
                            @csrf
                            <button type="submit"
                                class="w-full py-3 bg-white hover:bg-red-50 border-2 border-red-300 text-red-600 font-bold rounded-xl text-sm transition flex items-center justify-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                Reject Order
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Status Banner (non-pending) --}}
            @if($order->status !== 'Pending')
                <div class="rounded-2xl border shadow-sm overflow-hidden
                    {{ $order->status === 'Rejected' ? 'border-red-200 bg-red-50' : 'border-blue-200 bg-blue-50' }}">
                    <div class="px-6 py-5 flex items-center gap-3">
                        @if($order->status === 'Rejected')
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            <div>
                                <p class="font-bold text-red-700 text-sm">Order Rejected</p>
                                <p class="text-xs text-red-500 mt-0.5">You rejected this order</p>
                            </div>
                        @elseif($order->status === 'In Progress')
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="M12 6v6l4 2"/></svg>
                            <div>
                                <p class="font-bold text-blue-700 text-sm">In Progress</p>
                                <p class="text-xs text-blue-500 mt-0.5">Production has started</p>
                            </div>
                        @elseif($order->status === 'Completed')
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 13.01 9 10.01"/></svg>
                            <div>
                                <p class="font-bold text-green-700 text-sm">Completed</p>
                                <p class="text-xs text-green-500 mt-0.5">Order fulfilled successfully</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Financial Summary --}}
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h2 class="text-base font-bold text-neutral-900">Financial Summary</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-neutral-500">Order Value</span>
                        <span class="font-bold text-neutral-900">Rs {{ number_format($order->total_amount) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-neutral-500">Payment Terms</span>
                        <span class="font-semibold text-neutral-700">{{ str_replace('_', ' ', $order->payment_terms) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-neutral-500">Amount Received</span>
                        <span class="font-bold text-success-600">Rs {{ number_format($order->paid_amount ?? 0) }}</span>
                    </div>
                    <div class="pt-4 border-t border-dashed border-neutral-200 flex justify-between items-center">
                        <span class="text-sm font-bold text-neutral-900">Balance Due</span>
                        <span class="text-lg font-black text-neutral-900">
                            Rs {{ number_format($order->total_amount - ($order->paid_amount ?? 0)) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Shop Owner Info --}}
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h2 class="text-base font-bold text-neutral-900">Shop Owner</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 font-bold text-xl uppercase">
                            {{ substr($order->shopOwner->business_name ?? $order->shopOwner->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="font-bold text-neutral-900">{{ $order->shopOwner->business_name ?? $order->shopOwner->name }}</h4>
                            <p class="text-[11px] text-neutral-500">{{ $order->shopOwner->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('user.show', $order->shop_owner_id) }}"
                       class="block w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-center text-sm transition">
                        View Profile
                    </a>
                </div>
            </div>

            {{-- Production Stages (only when In Progress or Completed) --}}
            @if(in_array($order->status, ['In Progress', 'Completed']) && $order->stages->isNotEmpty())
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100 flex items-center justify-between">
                    <h2 class="text-base font-bold text-neutral-900">Production Stages</h2>
                    <span class="text-xs font-bold text-primary-600">{{ $order->progress_percent }}%</span>
                </div>
                {{-- Mini progress bar --}}
                <div class="w-full h-1.5 bg-neutral-100">
                    <div class="h-full {{ $order->progress_percent === 100 ? 'bg-success-500' : 'bg-primary-500' }} transition-all"
                         style="width: {{ $order->progress_percent }}%"></div>
                </div>
                <div class="p-4 space-y-2">
                    @foreach($order->stages as $index => $stage)
                    <div class="flex items-center gap-3 p-3 rounded-xl border transition-all
                        {{ $stage->status === 'completed' ? 'bg-success-50 border-success-200' : 'bg-neutral-50 border-neutral-100' }}">
                        <div class="shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                            {{ $stage->status === 'completed' ? 'bg-success-500 text-white' : 'bg-neutral-200 text-neutral-500' }}">
                            @if($stage->status === 'completed')
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold {{ $stage->status === 'completed' ? 'text-success-700' : 'text-neutral-700' }}">{{ $stage->name }}</p>
                            @if($stage->completed_at)
                                <p class="text-[10px] text-success-500 mt-0.5">{{ $stage->completed_at->format('M d, H:i') }}</p>
                            @endif
                        </div>
                        @if($order->status === 'In Progress')
                        <form action="{{ route('manufacturer.orders.stages.toggle', [$order->id, $stage->id]) }}" method="POST">
                            @csrf
                            @if($stage->status === 'completed')
                                <button type="submit" class="text-[9px] font-bold text-success-600 hover:text-neutral-500 uppercase tracking-wider">Undo</button>
                            @else
                                <button type="submit" class="text-[9px] font-bold text-primary-600 hover:text-primary-800 uppercase tracking-wider">Done</button>
                            @endif
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
</style>
@endsection
