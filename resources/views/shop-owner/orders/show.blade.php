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

            <!-- Delivery Status Card -->
            <div class="bg-white rounded-2xl p-6 border border-neutral-100 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-2xl">
                        🚚
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">Production Status</p>
                        <p class="text-lg font-bold text-neutral-900">
                            @if($order->status === 'Pending')
                                Awaiting Confirmation
                            @elseif($order->status === 'In Progress')
                                In Production
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

                            <!-- Processing -->
                            <div class="relative flex items-start gap-6">
                                <div class="mt-1.5 z-10">
                                    @if($order->status !== 'Pending')
                                        <div class="w-6 h-6 rounded-full bg-green-500 border-4 border-white shadow-sm flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-white border-4 border-neutral-100 shadow-sm"></div>
                                    @endif
                                </div>
                                <div class="flex-1 flex justify-between">
                                    <div>
                                        <h4 class="font-bold {{ $order->status === 'Pending' ? 'text-neutral-400' : 'text-neutral-900' }}">Manufacturer Confirmed</h4>
                                        <p class="text-sm text-neutral-500 mt-0.5">Manufacturer accepted the order</p>
                                    </div>
                                    <div class="text-sm font-medium text-neutral-400">--</div>
                                </div>
                            </div>

                            <!-- Production -->
                            <div class="relative flex items-start gap-6">
                                <div class="mt-1.5 z-10">
                                    @if($order->progress_percent > 0)
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
                                    <div class="text-sm font-medium text-neutral-400">--</div>
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
