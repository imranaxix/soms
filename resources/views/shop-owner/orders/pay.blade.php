@extends('layouts.app')

@section('title', 'Pay Order - SOMS')

@section('back_link', route('shop.orders.show', $order->id))
@section('page_title', 'Pay Order ' . $order->order_number)
@section('page_subtitle', 'Secure Checkout Gateway')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 animate-fade-in">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Payment Details Form -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-neutral-100 shadow-sm">
                
                {{-- Payment Methods Tabs --}}
                <div class="flex border-b border-neutral-100 mb-6">
                    @if($order->manufacturer->hasStripe())
                    <button id="tab-card" onclick="switchTab('card')" class="flex-1 py-3 text-center border-b-2 border-transparent text-sm font-bold text-neutral-400 hover:text-neutral-600 focus:outline-none transition-all">
                        💳 Credit / Debit Card
                    </button>
                    @endif
                    @if($safepayEnabled)
                    <button id="tab-safepay" onclick="switchTab('safepay')" class="flex-1 py-3 text-center border-b-2 border-transparent text-sm font-bold text-neutral-400 hover:text-neutral-600 focus:outline-none transition-all">
                        <img src="{{ asset('safepay-logo.png') }}" alt="Safepay" class="inline-block w-5 h-5 rounded-full object-cover align-middle"> Safepay
                    </button>
                    @endif
                </div>

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 text-sm font-medium rounded-xl">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Stripe Card Form --}}
                @if($order->manufacturer->hasStripe())
                <form id="stripe-card-form" class="">
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-neutral-400 uppercase tracking-widest mb-2 ml-1">
                            Cardholder Name
                        </label>
                        <input 
                            type="text" 
                            id="cardholder-name" 
                            placeholder="John Doe" 
                            required
                            class="w-full px-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all placeholder:text-neutral-300"
                        >
                    </div>

                    <!-- Card Details Input -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-neutral-400 uppercase tracking-widest mb-2 ml-1">
                            Card Information
                        </label>
                        <div id="card-element" class="w-full px-4 py-4 bg-neutral-50 border border-neutral-200 rounded-xl text-sm outline-none transition-all"></div>
                        <div id="card-errors" class="text-xs text-red-500 mt-2 ml-1" role="alert"></div>
                    </div>

                    <!-- Payment Amount Input -->
                    <div class="mb-8">
                        <label class="block text-xs font-bold text-neutral-400 uppercase tracking-widest mb-2 ml-1">
                            Payment Amount (Rs)
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-neutral-400 pointer-events-none">Rs</span>
                            <input 
                                type="number" 
                                id="stripe-amount"
                                min="1"
                                max="{{ $balanceDue }}"
                                step="any"
                                required
                                value="{{ old('amount', ($order->payment_terms === '50_advance' && $order->paid_amount == 0) ? ($order->total_amount / 2) : $balanceDue) }}"
                                class="w-full pl-10 pr-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all stripe-amount-input"
                            >
                        </div>
                    </div>

                    <!-- Pay Button -->
                    <button 
                        type="submit" 
                        id="stripe-submit-btn"
                        class="w-full py-4 bg-gradient-to-r from-indigo-600 to-blue-500 text-white rounded-2xl font-black text-center text-sm transition-all hover:opacity-95 shadow-lg shadow-indigo-100 flex items-center justify-center gap-2"
                    >
                        <span>Pay Securely with Card</span>
                    </button>
                </form>
                @endif

                {{-- Safepay Form --}}
                @if($safepayEnabled)
                <form id="safepay-form" action="{{ route('shop.orders.pay.safepay.initiate', $order->id) }}" method="POST" onsubmit="showProcessingState('Connecting to Safepay Secure Gateway...', 'safepay')" class="{{ !$order->manufacturer->hasStripe() ? '' : 'hidden' }}">
                    @csrf

                    <!-- Payment Amount Input -->
                    <div class="mb-8">
                        <label class="block text-xs font-bold text-neutral-400 uppercase tracking-widest mb-2 ml-1">
                            Payment Amount (Rs)
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-neutral-400 pointer-events-none">Rs</span>
                            <input 
                                type="number" 
                                name="amount" 
                                min="1"
                                max="{{ $balanceDue }}"
                                step="any"
                                required
                                value="{{ old('amount', ($order->payment_terms === '50_advance' && $order->paid_amount == 0) ? ($order->total_amount / 2) : $balanceDue) }}"
                                class="w-full pl-10 pr-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all safepay-amount-input"
                            >
                        </div>
                    </div>

                    <!-- Pay Button -->
                    <button 
                        type="submit" 
                        class="w-full py-4 bg-gradient-to-r from-slate-800 to-sky-600 text-white rounded-2xl font-black text-center text-sm transition-all hover:opacity-95 shadow-lg shadow-sky-100 flex items-center justify-center gap-2"
                    >
                        <span>Pay Securely with Safepay</span>
                    </button>
                </form>
                @endif

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
                    @if($order->manufacturer->profile_image)
                    <img src="{{ asset('storage/' . $order->manufacturer->profile_image) }}" alt="{{ $order->manufacturer->business_name ?? $order->manufacturer->name }}" class="w-10 h-10 rounded-xl object-cover">
                    @else
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 font-bold text-sm uppercase">
                        {{ substr($order->manufacturer->business_name ?? $order->manufacturer->name, 0, 1) }}
                    </div>
                    @endif
                    <div>
                        <h5 class="text-sm font-bold text-neutral-800">{{ $order->manufacturer->business_name ?? $order->manufacturer->name }}</h5>
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
            <div class="w-16 h-16 rounded-full border-4 border-orange-100 border-t-orange-500 animate-spin" id="spinner-color"></div>
        </div>
        <div class="space-y-2">
            <h3 class="text-xl font-black text-neutral-900" id="processing-title">Initiating Payment...</h3>
            <p class="text-sm text-neutral-500" id="processing-subtitle">Connecting to Secure Gateway. Please wait and do not close this window.</p>
        </div>
    </div>
</div>

@if($order->manufacturer->hasStripe())
<script src="https://js.stripe.com/v3/"></script>
<script>
    let stripe, elements, cardElement;
    
    document.addEventListener("DOMContentLoaded", function() {
        // Sync all amount inputs in case they enter on one tab and switch
        const amountInputs = document.querySelectorAll('.jc-amount-input, .stripe-amount-input, .safepay-amount-input');
        amountInputs.forEach((input) => {
            input.addEventListener('input', (e) => {
                amountInputs.forEach((other) => {
                    if (other !== e.target) other.value = e.target.value;
                });
            });
        });

        // Initialize Stripe if connected
        initStripe();

        // Set active tab on load — card tab takes priority when Stripe is enabled
        switchTab('card');
    });

    function initStripe() {
        if (!document.getElementById('card-element')) {
            return;
        }

        stripe = Stripe("{{ $order->manufacturer->stripe_publishable_key }}");
        elements = stripe.elements();

        const style = {
            base: {
                color: '#1a1a1a',
                fontFamily: '"Outfit", sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '15px',
                '::placeholder': {
                    color: '#d1d1d1'
                }
            },
            invalid: {
                color: '#ef4444',
                iconColor: '#ef4444'
            }
        };

        cardElement = elements.create('card', { style: style });
        cardElement.mount('#card-element');

        cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
    }

    const stripeForm = document.getElementById('stripe-card-form');
    if (stripeForm) {
        stripeForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const cardholderName = document.getElementById('cardholder-name').value;
            const amount = document.getElementById('stripe-amount').value;

            showProcessingState('Creating secure card session...', 'indigo');

            try {
                // 1. Contact Backend to Create Stripe PaymentIntent
                const res = await fetch("{{ route('shop.orders.pay.stripe.initiate', $order->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ amount: amount })
                });
                
                const data = await res.json();
                
                if (data.error) {
                    alert(data.error);
                    hideProcessingState();
                    return;
                }

                // 2. Card element is already mounted on page load; confirm payment
                showProcessingState('Securing transaction with Stripe...', 'indigo');

                // 3. Confirm card payment with Stripe
                const result = await stripe.confirmCardPayment(data.clientSecret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: cardholderName
                        }
                    }
                });

                if (result.error) {
                    document.getElementById('card-errors').textContent = result.error.message;
                    hideProcessingState();
                } else {
                    if (result.paymentIntent.status === 'succeeded') {
                        showProcessingState('Verifying payment status...', 'indigo');
                        
                        // 4. Confirm with Backend to record invoice update
                        const confirmRes = await fetch("{{ route('shop.orders.pay.stripe.confirm', $order->id) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({ payment_intent_id: result.paymentIntent.id })
                        });
                        
                        const confirmData = await confirmRes.json();
                        
                        if (confirmData.success) {
                            window.location.href = "{{ route('shop.orders.show', $order->id) }}?success=Payment confirmed successfully!";
                        } else {
                            alert('Card charge succeeded but platform recording failed: ' + (confirmData.error || 'Unknown Error') + '. Please contact support.');
                            hideProcessingState();
                        }
                    }
                }
            } catch (err) {
                console.error(err);
                alert('A checkout error occurred. Please try again.');
                hideProcessingState();
            }
        });
    }
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // When only Safepay is available (no Stripe), activate safepay tab by default
    @if(!$order->manufacturer->hasStripe() && $safepayEnabled)
    switchTab('safepay');
    @endif
});

function switchTab(method) {
    const tabs = { card: 'tab-card', safepay: 'tab-safepay' };
    const forms = { card: 'stripe-card-form', safepay: 'safepay-form' };
    const activeColors = {
        card: ['border-indigo-500', 'text-neutral-800'],
        safepay: ['border-sky-500', 'text-neutral-800']
    };

    Object.keys(tabs).forEach(key => {
        const tab = document.getElementById(tabs[key]);
        const form = document.getElementById(forms[key]);
        if (tab) {
            tab.classList.remove('border-red-500', 'border-indigo-500', 'border-sky-500', 'text-neutral-800');
            tab.classList.add('border-transparent', 'text-neutral-400');
        }
        if (form) form.classList.add('hidden');
    });

    const activeTab = document.getElementById(tabs[method]);
    const activeForm = document.getElementById(forms[method]);
    if (activeTab) {
        activeTab.classList.remove('border-transparent', 'text-neutral-400');
        activeTab.classList.add(...activeColors[method]);
    }
    if (activeForm) activeForm.classList.remove('hidden');

}

function showProcessingState(text = 'Initiating Payment...', color = 'orange') {
    document.getElementById('processing-title').textContent = text;
    document.getElementById('processing-overlay').classList.remove('hidden');
    
    const spinner = document.getElementById('spinner-color');
    if (color === 'indigo') {
        spinner.className = "w-16 h-16 rounded-full border-4 border-indigo-100 border-t-indigo-600 animate-spin";
    } else if (color === 'safepay') {
        spinner.className = "w-16 h-16 rounded-full border-4 border-sky-100 border-t-sky-600 animate-spin";
    } else {
        spinner.className = "w-16 h-16 rounded-full border-4 border-orange-100 border-t-orange-500 animate-spin";
    }
}

function hideProcessingState() {
    document.getElementById('processing-overlay').classList.add('hidden');
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
