@extends('layouts.app')

@section('title', 'Order Detail - SOMS')

@section('back_link', route('shop.orders.index'))
@section('page_title', 'Order ' . $order['order_number'])
@section('page_subtitle', 'Placed on ' . $order['placed_at'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content Area -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Delivery Status Card -->
            <div class="bg-white rounded-2xl p-6 border border-neutral-100 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-2xl">
                        🚚
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">Estimated Delivery</p>
                        <p class="text-lg font-bold text-neutral-900">{{ $order['estimated_delivery']['status'] }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">Due Date</p>
                    <p class="text-lg font-bold text-neutral-900">{{ $order['estimated_delivery']['due_date'] }}</p>
                </div>
            </div>

            <!-- Production Activity Card -->
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h2 class="text-lg font-bold text-neutral-900 mb-2">Production Activity</h2>
                    <span class="inline-flex px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[11px] font-bold uppercase tracking-wider border border-blue-100">
                        {{ $order['progress'] }}% Complete
                    </span>
                </div>
                <div class="p-8">
                    <div class="relative">
                        <!-- Vertical Line -->
                        <div class="absolute left-2.75 top-2 bottom-2 w-0.5 bg-neutral-100"></div>
                        
                        <!-- Timeline Items -->
                        <div class="space-y-12">
                            @foreach($order['timeline'] as $step)
                            <div class="relative flex items-start gap-6">
                                <!-- Step Dot -->
                                <div class="mt-1.5 z-10">
                                    @if($step['status'] == 'completed')
                                        <div class="w-6 h-6 rounded-full bg-green-500 border-4 border-white shadow-sm flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-white border-4 border-neutral-100 shadow-sm"></div>
                                    @endif
                                </div>
                                
                                <!-- Step Content -->
                                <div class="flex-1 flex justify-between">
                                    <div>
                                        <h4 class="font-bold text-neutral-900 {{ $step['status'] == 'pending' ? 'text-neutral-400' : '' }}">
                                            {{ $step['label'] }}
                                        </h4>
                                        <p class="text-sm text-neutral-500 mt-0.5">
                                            {{ $step['desc'] }}
                                        </p>
                                    </div>
                                    <div class="text-sm font-medium text-neutral-400">
                                        {{ $step['date'] }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details Card -->
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h2 class="text-lg font-bold text-neutral-900">Product Details</h2>
                </div>
                <div class="p-0">
                    <table class="w-full text-left">
                        <tbody class="divide-y divide-neutral-50">
                            <tr class="group">
                                <td class="px-6 py-6 text-sm text-neutral-500 w-1/3">Item</td>
                                <td class="px-6 py-6 text-sm font-bold text-neutral-900 text-right">{{ $order['details']['item'] }}</td>
                            </tr>
                            <tr class="group">
                                <td class="px-6 py-6 text-sm text-neutral-500">Quantity</td>
                                <td class="px-6 py-6 text-sm font-bold text-neutral-900 text-right">{{ $order['details']['quantity'] }}</td>
                            </tr>
                            <tr class="group">
                                <td class="px-6 py-6 text-sm text-neutral-500">Price per Unit</td>
                                <td class="px-6 py-6 text-sm font-bold text-neutral-900 text-right">₹{{ number_format($order['details']['price_per_unit'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment History Card -->
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h2 class="text-lg font-bold text-neutral-900">Payment History</h2>
                </div>
                <div class="p-0">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-neutral-50/50 border-b border-neutral-100 text-[11px] font-bold text-neutral-400 uppercase tracking-widest">
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Method</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-50">
                            @foreach($order['payments'] as $payment)
                            <tr>
                                <td class="px-6 py-5 text-sm text-neutral-600">{{ $payment['date'] }}</td>
                                <td class="px-6 py-5 text-sm font-bold text-neutral-900">₹{{ number_format($payment['amount']) }}</td>
                                <td class="px-6 py-5 text-sm text-neutral-500 lowercase">{{ $payment['method'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                        <span class="font-bold text-neutral-900">₹{{ number_format($order['financial']['subtotal']) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-neutral-500">Total Paid</span>
                        <span class="font-bold text-neutral-900">₹{{ number_format($order['financial']['paid']) }}</span>
                    </div>
                    <div class="pt-6 border-t border-dashed border-neutral-200 flex justify-between items-center">
                        <span class="text-sm font-bold text-neutral-900">Balance Due</span>
                        <span class="text-xl font-black text-neutral-900">₹{{ number_format($order['financial']['balance']) }}</span>
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
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 font-bold text-xl">
                            {{ substr($order['manufacturer']['name'], 0, 1) }}
                        </div>
                        <div>
                            <h4 class="font-bold text-neutral-900">{{ $order['manufacturer']['name'] }}</h4>
                            <p class="text-[11px] font-bold text-indigo-600 uppercase tracking-tight">{{ $order['manufacturer']['status'] }}</p>
                        </div>
                    </div>
                    <button class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm transition shadow-md shadow-indigo-200">
                        View Profile
                    </button>
                    <div class="mt-8 pt-6 border-t border-neutral-50 text-center">
                        <p class="text-[11px] text-neutral-400 font-medium italic">
                            Last updated: {{ $order['manufacturer']['last_updated'] }}
                        </p>
                    </div>
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
