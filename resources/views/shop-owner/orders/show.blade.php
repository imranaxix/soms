@extends('layouts.app')

@section('title', 'Order Detail - SOMS')

@section('back_link', route('shop.orders.index'))
@section('page_title', 'Order ' . $order->order_number)
@section('page_subtitle', 'Placed on ' . $order->created_at->format('M d, Y'))


@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-24">
        <!-- Main Content Area -->
        <div class="lg:col-span-2 space-y-8">
            
        <!-- Flash Messages -->
            @if(session('success'))
            <div class="mb-6 flex items-center gap-3 px-5 py-4 rounded-2xl bg-green-50 border border-green-200 text-green-800 text-sm font-medium">
                <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="mb-6 flex items-center gap-3 px-5 py-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium">
                <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
            @endif

            <!-- Product Hero Card -->
            <div class="bg-white rounded-3xl border border-neutral-100 shadow-sm overflow-hidden mb-8">
                <div class="flex flex-col md:flex-row">
                    <!-- Product Image -->
                    <div class="w-full md:w-48 h-48 bg-neutral-50 flex items-center justify-center p-4">
                        @if($order->variant && $order->variant->image)
                            <img src="{{ asset('storage/' . $order->variant->image) }}" alt="{{ $order->product->name }}" class="w-full h-full object-cover rounded-xl shadow-sm">
                        @else
                            <div class="w-full h-full bg-primary-50 rounded-xl flex flex-col items-center justify-center text-primary-200">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                                <span class="text-[10px] font-bold uppercase mt-2 tracking-widest">No Image</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Product Info -->
                    <div class="flex-1 p-6 flex flex-col justify-center">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[10px] font-bold text-primary-600 uppercase tracking-widest mb-1">{{ $order->product->category ?? 'Manufacturing' }}</p>
                                <h3 class="text-2xl font-black text-neutral-900 leading-tight">{{ $order->product->name }}</h3>
                                <div class="flex items-center gap-3 mt-2">
                                    <span class="px-2 py-0.5 bg-neutral-100 text-neutral-600 rounded text-[11px] font-bold">{{ $order->variant->variant_name ?? 'Standard Variant' }}</span>
                                    <span class="text-neutral-300">•</span>
                                    <span class="text-sm font-bold text-neutral-500">{{ $order->quantity }} {{ $order->unit }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest mb-1">Total Value</p>
                                <p class="text-xl font-black text-primary-600">Rs {{ number_format($order->total_amount) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Banner: Processing Payment --}}
            @php
                $pendingPayment = $order->payments()->where('status', 'pending')->latest()->first();
            @endphp
            @if($pendingPayment)
            <div class="rounded-2xl border border-orange-200 bg-orange-50 p-5 flex items-center justify-between gap-4" id="payment-pending-banner">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-orange-500 shrink-0">
                        <svg class="animate-spin" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10" stroke-opacity="0.2"/>
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-orange-800 text-sm">Payment of Rs {{ number_format($pendingPayment->amount) }} is processing</p>
                        <p class="text-xs text-orange-600 mt-0.5">Please approve the MPIN request on your JazzCash app. Polling for confirmation...</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Banner: Cancel (Pending) -->
            @if($order->status === 'Pending')
            <div class="rounded-2xl border border-red-200 bg-red-50 p-5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center text-red-500 shrink-0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-red-700 text-sm">Want to cancel this order?</p>
                        <p class="text-xs text-red-500 mt-0.5">You can only cancel while the order is still pending manufacturer acceptance.</p>
                    </div>
                </div>
                <form action="{{ route('shop.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order? This cannot be undone.');">
                    @csrf
                    <button type="submit" class="shrink-0 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition shadow-sm shadow-red-200 text-sm whitespace-nowrap">
                        Cancel Order
                    </button>
                </form>
            </div>
            @endif

            <!-- Action Banner: Confirm Delivery (Delivered) -->
            @if($order->status === 'Delivered')
            <div class="rounded-2xl border border-green-200 bg-green-50 p-5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-green-600 shrink-0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7l-8 8-4-4"/><path d="M4 4h16v16H4z" stroke-opacity="0"/><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-green-700 text-sm">Your order has been delivered!</p>
                        <p class="text-xs text-green-600 mt-0.5">Please confirm you have received the goods to complete this order.</p>
                    </div>
                </div>
                <form action="{{ route('shop.orders.confirm-delivery', $order->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="shrink-0 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition shadow-sm shadow-green-200 text-sm whitespace-nowrap">
                        ✓ Confirm Receipt
                    </button>
                </form>
            </div>
            @endif

            <!-- Delivery Status Card -->
            <div class="bg-white rounded-2xl p-6 border border-neutral-100 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl
                        {{ $order->status === 'Rejected' || $order->status === 'Cancelled' ? 'bg-red-50' : ($order->status === 'Delivered' ? 'bg-purple-50' : ($order->status === 'Completed' ? 'bg-green-50' : 'bg-orange-50')) }}">
                        {{ $order->status === 'Rejected' || $order->status === 'Cancelled' ? '❌' : ($order->status === 'Delivered' ? '📦' : ($order->status === 'Completed' ? '✅' : '🚚')) }}
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">Order Status</p>
                        <p class="text-lg font-bold
                            {{ $order->status === 'Rejected' || $order->status === 'Cancelled' ? 'text-red-600' : ($order->status === 'Delivered' ? 'text-purple-600' : ($order->status === 'Completed' ? 'text-green-600' : 'text-neutral-900')) }}">
                            @if($order->status === 'Pending')
                                Awaiting Manufacturer Confirmation
                            @elseif($order->status === 'In Progress')
                                Accepted — In Production
                            @elseif($order->status === 'Rejected')
                                Order Rejected by Manufacturer
                            @elseif($order->status === 'Cancelled')
                                Order Cancelled
                            @elseif($order->status === 'Delivered')
                                Delivered — Awaiting Your Confirmation
                            @else
                                {{ $order->status }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">Expected Delivery</p>
                    <p class="text-lg font-bold text-neutral-900">{{ $order->due_date->format('M d, Y') }}</p>
                </div>
            </div>

            <!-- Production Activity Card -->
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h2 class="text-lg font-bold text-neutral-900 mb-2">Production Activity</h2>
                    <span class="inline-flex px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[11px] font-bold uppercase tracking-wider border border-blue-100">
                        {{ $order->progress_percent }}% Complete
                    </span>
                </div>
                <div class="p-8">
                    <div class="relative">
                        <!-- Vertical Line -->
                        <div class="absolute left-2.75 top-2 bottom-2 w-0.5 bg-neutral-100"></div>
                        
                        <!-- Timeline Items (Semi-Dynamic based on status) -->
                        <div class="space-y-12">
                            <!-- Order Placed -->
                            <div class="relative flex items-start gap-6">
                                <div class="mt-1.5 z-10">
                                    <div class="w-6 h-6 rounded-full bg-green-500 border-4 border-white shadow-sm flex items-center justify-center">
                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                    </div>
                                </div>
                                <div class="flex-1 flex justify-between">
                                    <div>
                                        <h4 class="font-bold text-neutral-900">Order Placed</h4>
                                        <p class="text-sm text-neutral-500 mt-0.5">Order request sent to manufacturer</p>
                                    </div>
                                    <div class="text-sm font-medium text-neutral-400">
                                        {{ $order->created_at->format('M d, H:i') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Manufacturer Decision -->
                            <div class="relative flex items-start gap-6">
                                <div class="mt-1.5 z-10">
                                    @if($order->status === 'Cancelled')
                                        <div class="w-6 h-6 rounded-full bg-red-500 border-4 border-white shadow-sm flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        </div>
                                    @elseif($order->status === 'In Progress' || $order->status === 'Completed' || $order->status === 'Delivered')
                                        <div class="w-6 h-6 rounded-full bg-green-500 border-4 border-white shadow-sm flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        </div>
                                    @elseif($order->status === 'Rejected')
                                        <div class="w-6 h-6 rounded-full bg-red-500 border-4 border-white shadow-sm flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-white border-4 border-neutral-100 shadow-sm"></div>
                                    @endif
                                </div>
                                <div class="flex-1 flex justify-between">
                                    <div>
                                        <h4 class="font-bold
                                            {{ $order->status === 'Pending' ? 'text-neutral-400' : ($order->status === 'Rejected' || $order->status === 'Cancelled' ? 'text-red-600' : 'text-neutral-900') }}">
                                            @if($order->status === 'Rejected') Manufacturer Rejected
                                            @elseif($order->status === 'Cancelled') Order Cancelled
                                            @elseif($order->status === 'Pending') Awaiting Manufacturer Decision
                                            @else Manufacturer Accepted
                                            @endif
                                        </h4>
                                        <p class="text-sm text-neutral-500 mt-0.5">
                                            @if($order->status === 'Rejected') The manufacturer declined this order
                                            @elseif($order->status === 'Cancelled') You cancelled this order
                                            @elseif($order->status === 'Pending') No response yet
                                            @else Manufacturer confirmed and started production
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-sm font-medium text-neutral-400">
                                        {{ $order->status !== 'Pending' ? $order->updated_at->format('M d, H:i') : '--' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Production Stages -->
                            @if(in_array($order->status, ['In Progress', 'Completed', 'Delivered']) && $order->stages->isNotEmpty())
                                @foreach($order->stages as $stage)
                                <div class="relative flex items-start gap-6">
                                    <div class="mt-1.5 z-10">
                                        @if($stage->status === 'completed')
                                            <div class="w-6 h-6 rounded-full bg-green-500 border-4 border-white shadow-sm flex items-center justify-center">
                                                <div class="w-2 h-2 bg-white rounded-full"></div>
                                            </div>
                                        @elseif($order->status === 'Completed' || $order->status === 'Delivered' || $order->progress_percent > 0)
                                            <div class="w-6 h-6 rounded-full bg-white border-4 {{ $stage->status === 'pending' ? 'border-primary-400' : 'border-neutral-200' }} shadow-sm"></div>
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-white border-4 border-neutral-100 shadow-sm"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1 flex justify-between">
                                        <div>
                                            <h4 class="font-bold {{ $stage->status === 'completed' ? 'text-green-700' : 'text-neutral-900' }}">{{ $stage->name }}</h4>
                                            @if($stage->description)
                                                <p class="text-xs text-neutral-500 mt-0.5">{{ $stage->description }}</p>
                                            @endif
                                        </div>
                                        <div class="text-xs font-medium {{ $stage->status === 'completed' ? 'text-green-600' : 'text-neutral-400' }}">
                                            {{ $stage->completed_at ? $stage->completed_at->format('M d, H:i') : '--' }}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <!-- Generic Production Stage (Fallback) -->
                                <div class="relative flex items-start gap-6">
                                    <div class="mt-1.5 z-10">
                                        @if($order->status === 'Completed' || $order->status === 'Delivered' || $order->progress_percent > 0)
                                            <div class="w-6 h-6 rounded-full bg-green-500 border-4 border-white shadow-sm flex items-center justify-center">
                                                <div class="w-2 h-2 bg-white rounded-full"></div>
                                            </div>
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-white border-4 border-neutral-100 shadow-sm"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1 flex justify-between">
                                        <div>
                                            <h4 class="font-bold {{ $order->progress_percent == 0 ? 'text-neutral-400' : 'text-neutral-900' }}">In Production</h4>
                                            <p class="text-sm text-neutral-500 mt-0.5">Items are being manufactured</p>
                                        </div>
                                        <div class="text-sm font-medium text-neutral-400">
                                            {{ ($order->status === 'Completed' || $order->status === 'Delivered') && $order->updated_at ? $order->updated_at->format('M d, H:i') : '--' }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Delivery / Completion Stage -->
                            <div class="relative flex items-start gap-6">
                                <div class="mt-1.5 z-10">
                                    @if($order->status === 'Completed')
                                        <div class="w-6 h-6 rounded-full bg-green-500 border-4 border-white shadow-sm flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        </div>
                                    @elseif($order->status === 'Delivered')
                                        <div class="w-6 h-6 rounded-full bg-white border-4 border-purple-500 shadow-sm"></div>
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-white border-4 border-neutral-100 shadow-sm"></div>
                                    @endif
                                </div>
                                <div class="flex-1 flex justify-between">
                                    <div>
                                        <h4 class="font-bold {{ $order->status === 'Completed' ? 'text-green-700' : ($order->status === 'Delivered' ? 'text-purple-600' : 'text-neutral-400') }}">Delivered & Completed</h4>
                                        <p class="text-sm text-neutral-500 mt-0.5">
                                            @if($order->status === 'Completed') Delivery confirmed
                                            @elseif($order->status === 'Delivered') Waiting for your confirmation
                                            @else Pending completion
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-sm font-medium text-neutral-400">
                                        {{ $order->status === 'Completed' ? $order->updated_at->format('M d, H:i') : '--' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <!-- Sidebar Area -->
        <div class="space-y-8">
            <!-- Financial Summary -->
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h2 class="text-lg font-bold text-neutral-900">Financial Summary</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-neutral-500">Subtotal</span>
                        <span class="font-bold text-neutral-900">Rs {{ number_format($order->total_amount) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-neutral-500">Total Paid</span>
                        <span class="font-bold text-success-600">Rs {{ number_format($order->paid_amount) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[11px] uppercase tracking-wider text-neutral-400">
                        <span>Payment Terms</span>
                        <span>{{ str_replace('_', ' ', $order->payment_terms) }}</span>
                    </div>
                    <div class="pt-6 border-t border-dashed border-neutral-200 flex justify-between items-center">
                        <span class="text-sm font-bold text-neutral-900">Balance Due</span>
                        <span class="text-xl font-black text-neutral-900">Rs {{ number_format($order->total_amount - $order->paid_amount) }}</span>
                    </div>

                    @if($order->total_amount - $order->paid_amount > 0)
                        @if($order->manufacturer->hasJazzCash())
                            <div class="pt-4">
                                <a href="{{ route('shop.orders.pay', $order->id) }}" class="block w-full py-3 bg-gradient-to-r from-[#e8001a] to-[#ff6600] text-white rounded-xl font-black text-center text-sm transition hover:opacity-90 shadow-md shadow-orange-100">
                                    Pay Now with JazzCash
                                </a>
                            </div>
                        @else
                            <div class="pt-4 p-3 bg-neutral-50 border border-neutral-200 rounded-xl text-center text-xs text-neutral-500">
                                Manufacturer has not set up direct JazzCash details yet.
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Manufacturer Card -->
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h2 class="text-lg font-bold text-neutral-900">Manufacturer</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 font-bold text-xl uppercase">
                            {{ substr($order->manufacturer->business_name ?? $order->manufacturer->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="font-bold text-neutral-900">{{ $order->manufacturer->business_name ?? $order->manufacturer->name }}</h4>
                            <p class="text-[11px] font-bold text-indigo-600 uppercase tracking-tight">Verified Supply Partner</p>
                        </div>
                    </div>
                    <a href="{{ route('user.show', $order->manufacturer_id) }}" class="block w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-center text-sm transition shadow-md shadow-indigo-200">
                        View Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    @if($pendingPayment)
        const checkPaymentInterval = setInterval(() => {
            fetch("{{ route('shop.orders.payment-status', $order->id) }}")
                .then(response => response.json())
                .then(data => {
                    if (data.status && data.status !== 'pending') {
                        clearInterval(checkPaymentInterval);
                        window.location.reload();
                    }
                })
                .catch(err => console.error("Error polling payment status:", err));
        }, 3000);
    @endif
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
