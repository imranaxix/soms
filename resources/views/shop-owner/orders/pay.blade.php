@extends('layouts.app')

@section('title', 'Pay Order - SOMS')

@section('back_link', route('shop.orders.show', $order->id))
@section('page_title', 'Pay Order #' . $order->order_number)
@section('page_subtitle', 'Secure Checkout via JazzCash')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 animate-fade-in">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Payment Details Form -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-neutral-100 shadow-sm">
                <h3 class="text-lg font-black text-neutral-900 mb-6">Payment Authorization</h3>

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 text-sm font-medium rounded-xl">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="payment-form" action="{{ route('shop.orders.pay.initiate', $order->id) }}" method="POST" onsubmit="showProcessingState()">
                    @csrf

                    <!-- Mobile Number Input -->
                    <div class="mb-6">
                        <label for="shop_owner_mobile" class="block text-xs font-bold text-neutral-400 uppercase tracking-widest mb-2 ml-1">
                            Your JazzCash Mobile Number
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold pointer-events-none">🇵🇰</span>
                            <input 
                                type="tel" 
                                name="shop_owner_mobile" 
                                id="shop_owner_mobile"
                                placeholder="03001234567"
                                maxlength="11"
                                required
                                class="w-full pl-12 pr-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all placeholder:text-neutral-300 placeholder:font-normal"
                            >
                        </div>
                        <p class="text-[10px] text-neutral-400 mt-1.5 ml-1">An MPIN prompt will be sent to this mobile account to confirm the transaction.</p>
                    </div>

                    <!-- CNIC Last 6 Digits Input -->
                    <div class="mb-6">
                        <label for="cnic" class="block text-xs font-bold text-neutral-400 uppercase tracking-widest mb-2 ml-1">
                            Last 6 Digits of Your CNIC
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-neutral-400 pointer-events-none">💳</span>
                            <input
                                type="text"
                                name="cnic"
                                id="cnic"
                                placeholder="345678"
                                maxlength="6"
                                inputmode="numeric"
                                pattern="[0-9]{6}"
                                required
                                value="{{ old('cnic') }}"
                                class="w-full pl-10 pr-4 py-3.5 bg-neutral-50 border {{ $errors->has('cnic') ? 'border-red-400 focus:border-red-500 focus:ring-red-500/10' : 'border-neutral-200 focus:border-primary-500 focus:ring-primary-500/10' }} rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 outline-none transition-all placeholder:text-neutral-300 placeholder:font-normal"
                            >
                        </div>
                        @error('cnic')
                            <p class="text-[11px] text-red-600 mt-1.5 ml-1 font-medium">{{ $message }}</p>
                        @enderror
                        @if(!$errors->has('cnic'))
                            <p class="text-[10px] text-neutral-400 mt-1.5 ml-1">Required by JazzCash v2.0 to authenticate your mobile wallet.</p>
                        @endif
                    </div>

                    <!-- Payment Amount Input -->
                    <div class="mb-8">
                        <label for="amount" class="block text-xs font-bold text-neutral-400 uppercase tracking-widest mb-2 ml-1">
                            Payment Amount (Rs)
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-neutral-400 pointer-events-none">Rs</span>
                            <input 
                                type="number" 
                                name="amount" 
                                id="amount"
                                min="1"
                                max="{{ $balanceDue }}"
                                step="any"
                                required
                                value="{{ old('amount', ($order->payment_terms === '50_advance' && $order->paid_amount == 0) ? ($order->total_amount / 2) : $balanceDue) }}"
                                class="w-full pl-10 pr-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all"
                            >
                        </div>
                        <p class="text-[10px] text-neutral-400 mt-1.5 ml-1">
                            Suggested based on terms: 
                            <span class="font-bold text-neutral-600">
                                @if($order->payment_terms === 'full_advance')
                                    Rs {{ number_format($order->total_amount) }} (Full Advance)
                                @elseif($order->payment_terms === '50_advance')
                                    Rs {{ number_format($order->total_amount / 2) }} (50% Upfront / 50% Delivery)
                                @else
                                    Rs {{ number_format($balanceDue) }} (Pay on Delivery)
                                @endif
                            </span>
                        </p>
                    </div>

                    <!-- Pay Button -->
                    <button 
                        type="submit" 
                        id="submit-btn"
                        class="w-full py-4 bg-gradient-to-r from-[#e8001a] to-[#ff6600] text-white rounded-2xl font-black text-center text-sm transition-all hover:opacity-95 shadow-lg shadow-orange-100 flex items-center justify-center gap-2"
                    >
                        <span>Pay Securely with JazzCash</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Order Summary Side Panel -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-neutral-100 shadow-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-widest mb-4">Order Summary</h4>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3 pb-4 border-b border-neutral-100">
                        <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center text-primary-500 shrink-0 font-bold uppercase text-sm">
                            {{ substr($order->product->name, 0, 1) }}
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-neutral-800 line-clamp-1">{{ $order->product->name }}</h5>
                            <p class="text-xs text-neutral-400 font-medium mt-0.5">{{ $order->quantity }} {{ $order->unit }}</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <span class="text-neutral-400 font-medium">Total Amount</span>
                        <span class="font-bold text-neutral-700">Rs {{ number_format($order->total_amount) }}</span>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <span class="text-neutral-400 font-medium">Already Paid</span>
                        <span class="font-bold text-success-600">Rs {{ number_format($order->paid_amount) }}</span>
                    </div>

                    <div class="flex justify-between items-center text-xs pb-4 border-b border-neutral-100">
                        <span class="text-neutral-400 font-medium">Payment Terms</span>
                        <span class="font-bold text-neutral-700 capitalize">{{ str_replace('_', ' ', $order->payment_terms) }}</span>
                    </div>

                    <div class="flex justify-between items-center pt-2">
                        <span class="text-sm font-bold text-neutral-900">Balance Due</span>
                        <span class="text-lg font-black text-neutral-900">Rs {{ number_format($balanceDue) }}</span>
                    </div>
                </div>
            </div>

            <!-- Payee details -->
            <div class="bg-white rounded-3xl p-6 border border-neutral-100 shadow-sm">
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-widest mb-3">Paying to</h4>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 font-bold text-sm uppercase">
                        {{ substr($order->manufacturer->business_name ?? $order->manufacturer->name, 0, 1) }}
                    </div>
                    <div>
                        <h5 class="text-sm font-bold text-neutral-800">{{ $order->manufacturer->business_name ?? $order->manufacturer->name }}</h5>
                        <p class="text-[10px] text-success-600 font-bold uppercase mt-0.5">Title: {{ $order->manufacturer->jazzcash_account_title }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Processing Screen Overlay (Hidden by default) -->
<div id="processing-overlay" class="fixed inset-0 bg-neutral-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-6 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-8 text-center space-y-6 shadow-2xl animate-scale-in">
        <div class="flex justify-center">
            <div class="w-16 h-16 rounded-full border-4 border-orange-100 border-t-orange-500 animate-spin"></div>
        </div>
        <div class="space-y-2">
            <h3 class="text-xl font-black text-neutral-900">Initiating Payment...</h3>
            <p class="text-sm text-neutral-500">Connecting to JazzCash Secure Gateway. Please wait and do not close this window.</p>
        </div>
    </div>
</div>

<script>
function showProcessingState() {
    document.getElementById('processing-overlay').classList.remove('hidden');
    document.getElementById('submit-btn').disabled = true;
    document.getElementById('submit-btn').classList.add('opacity-70');
}
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes scale-in {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.animate-fade-in {
    animation: fade-in 0.4s ease-out forwards;
}
.animate-scale-in {
    animation: scale-in 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}
</style>
@endsection
