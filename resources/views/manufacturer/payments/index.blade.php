@extends('layouts.app')

@section('title', 'Payments - Manufacturer')
@section('page_title', 'Payments')
@section('page_subtitle', 'Track your incoming payments')

@section('header_actions')
    @if(request('tab') !== 'methods')
    <form method="GET" action="{{ route('manufacturer.payments.index') }}" class="flex items-center">
        @if(request('tab'))
            <input type="hidden" name="tab" value="{{ request('tab') }}">
        @endif
        <select name="period" onchange="this.form.submit()" class="bg-white border border-neutral-200 rounded-lg px-4 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-500 shadow-sm cursor-pointer">
            <option value="all" {{ ($period ?? 'all') === 'all' ? 'selected' : '' }}>All Time</option>
            <option value="30days" {{ ($period ?? '') === '30days' ? 'selected' : '' }}>Last 30 Days</option>
            <option value="pending" {{ ($period ?? '') === 'pending' ? 'selected' : '' }}>Pending Only</option>
        </select>
    </form>
    @endif
@endsection

@section('content')
<div class="space-y-8">

    {{-- Session Alerts --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-5 py-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-medium">
        <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 px-5 py-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium">
        <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
        {{ session('error') }}
    </div>
    @endif

    <!-- Payment Tabs -->
    <div class="flex gap-8 border-b border-neutral-200">
        <button onclick="switchPaymentTab('transactions')" id="tab-transactions"
            class="pb-4 text-sm font-bold border-b-2 transition-colors {{ request('tab') !== 'methods' ? 'text-primary-600 border-primary-600' : 'text-neutral-400 border-transparent hover:text-neutral-600' }}">
            Transactions
        </button>
        <button onclick="switchPaymentTab('methods')" id="tab-methods"
            class="pb-4 text-sm font-bold border-b-2 transition-colors {{ request('tab') === 'methods' ? 'text-primary-600 border-primary-600' : 'text-neutral-400 border-transparent hover:text-neutral-600' }}">
            Payment Methods
        </button>
    </div>

    {{-- ==================== TRANSACTIONS TAB ==================== --}}
    <div id="panel-transactions" class="{{ request('tab') === 'methods' ? 'hidden' : '' }}">
        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 py-2">
            <div class="bg-white px-6 py-8 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-6">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M12 8C12 8.55228 11.5523 9 11 9C10.4477 9 10 8.55228 10 8C10 7.44772 10.4477 7 11 7C11.5523 7 12 7.44772 12 8Z" fill="currentColor"/><path d="M12 12C12 12.5523 11.5523 13 11 13C10.4477 13 10 12.5523 10 12C10 11.4477 10.4477 11 11 11C11.5523 11 12 11.4477 12 12Z" fill="currentColor"/><path d="M12 16C12 16.5523 11.5523 17 11 17C10.4477 17 10 16.5523 10 16C10 15.4477 10.4477 15 11 15C11.5523 15 12 15.4477 12 16Z" fill="currentColor"/><path d="M19 19C19 20.1046 18.1046 21 17 21H7C5.89543 21 5 20.1046 5 19V5C5 3.89543 5.89543 3 7 3H17C18.1046 3 19 3.89543 19 5V19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div>
                    <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Total Order Value</p>
                    <h3 class="text-2xl font-bold text-neutral-900">Rs {{ number_format($stats['totalOrderValue']) }}</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-6">
                <div class="w-14 h-14 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div>
                    <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Total Paid</p>
                    <h3 class="text-2xl font-bold text-neutral-900">Rs {{ number_format($stats['totalPaid']) }}</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-6">
                <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M12 8V12L15 15M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div>
                    <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Pending Balance</p>
                    <h3 class="text-2xl font-bold text-neutral-900">Rs {{ number_format($stats['pendingBalance']) }}</h3>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-neutral-50 bg-neutral-50/30">
                <h2 class="text-base font-bold text-neutral-900">Recent Transactions</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-primary-600 text-white">
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Date</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Order ID</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Received From</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-center">Method</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($transactions as $trx)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-6 py-5 text-sm font-medium text-neutral-600">{{ \Carbon\Carbon::parse($trx['date'])->format('M d, Y') }}</td>
                            <td class="px-6 py-5 text-sm font-bold text-primary-600">
                                <a href="{{ route('manufacturer.orders.show', $trx['order_id']) }}">{{ $trx['order_number'] }}</a>
                            </td>
                            <td class="px-6 py-5 text-sm text-neutral-600">{{ $trx['received_from'] }}</td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center px-3 py-1 bg-neutral-100 border border-neutral-200 rounded text-[10px] font-bold text-neutral-500 uppercase tracking-tighter italic">
                                    {{ $trx['method'] }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-right font-bold text-success-600">Rs {{ number_format($trx['amount']) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-neutral-400">No completed payments received yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Order Balances -->
        <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-neutral-50 bg-neutral-50/30">
                <h2 class="text-base font-bold text-neutral-900">Order Balances</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-primary-600 text-white">
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Order #</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Product</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Shop Owner</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-right">Total Amount</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-right">Paid</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-right">Balance</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($orderBalances as $item)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-6 py-5 text-sm font-bold text-primary-600">
                                <a href="{{ route('manufacturer.orders.show', $item['order_id']) }}">{{ $item['order_number'] }}</a>
                            </td>
                            <td class="px-6 py-5 text-sm text-neutral-600">{{ $item['product'] }}</td>
                            <td class="px-6 py-5 text-sm text-neutral-500">{{ $item['shop_owner'] }}</td>
                            <td class="px-6 py-5 text-right font-bold text-neutral-900">Rs {{ number_format($item['total']) }}</td>
                            <td class="px-6 py-5 text-right font-bold text-success-600">Rs {{ number_format($item['paid']) }}</td>
                            <td class="px-6 py-5 text-right font-bold text-orange-600">Rs {{ number_format($item['balance']) }}</td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                    {{ $item['status'] === 'Completed' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $item['status'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-neutral-400">No active orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ==================== PAYMENT METHODS TAB ==================== --}}
    <div id="panel-methods" class="{{ request('tab') !== 'methods' ? 'hidden' : '' }}">
        <div class="max-w-3xl mx-auto space-y-6">

            {{-- Stripe Card --}}
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100 bg-neutral-50/60 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('stripe-logo.svg') }}" alt="Stripe" class="w-6 h-6 object-contain">
                        </div>
                        <div>
                            <h3 class="font-black text-neutral-900 text-sm">Credit &amp; Debit Cards (Stripe)</h3>
                            <p class="text-xs text-neutral-400 mt-0.5">Receive card payments globally from Shop Owners directly into your bank account.</p>
                        </div>
                    </div>
                    @if($user->hasStripe())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 font-bold text-xs rounded-full border border-green-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-600 animate-pulse"></span>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-neutral-100 text-neutral-500 font-bold text-xs rounded-full">
                            Not configured
                        </span>
                    @endif
                </div>

                <form action="{{ route('manufacturer.payment-methods.update') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-neutral-700 mb-1.5">Publishable Key</label>
                            <input type="text" name="stripe_publishable_key" value="{{ $user->stripe_publishable_key ?? '' }}"
                                   placeholder="pk_live_..."
                                   class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all placeholder:text-neutral-400">
                            @error('stripe_publishable_key')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-neutral-700 mb-1.5">Secret Key</label>
                            <input type="password" name="stripe_secret_key" value="{{ $user->stripe_secret_key ?? '' }}"
                                   placeholder="sk_live_..."
                                   class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all placeholder:text-neutral-400">
                            @error('stripe_secret_key')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-6 py-2.5 bg-primary-600 text-white rounded-xl font-bold text-sm hover:bg-primary-700 active:scale-[0.98] transition-all shadow-lg shadow-primary-500/25">
                            Save Stripe Keys
                        </button>
                    </div>
                </form>
            </div>

            {{-- Safepay Card --}}
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100 bg-neutral-50/60 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('safepay-logo.png') }}" alt="Safepay" class="w-6 h-6 object-contain">
                        </div>
                        <div>
                            <h3 class="font-black text-neutral-900 text-sm">Safepay</h3>
                            <p class="text-xs text-neutral-400 mt-0.5">Accept payments via Safepay from Shop Owners across supported regions.</p>
                        </div>
                    </div>
                    @if($user->hasSafepay())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 font-bold text-xs rounded-full border border-green-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-600 animate-pulse"></span>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-neutral-100 text-neutral-500 font-bold text-xs rounded-full">
                            Not configured
                        </span>
                    @endif
                </div>

                <form action="{{ route('manufacturer.payment-methods.update') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-neutral-700 mb-1.5">Public Key</label>
                            <input type="text" name="safepay_api_key" value="{{ $user->safepay_api_key ?? '' }}"
                                   placeholder="pub_..."
                                   class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all placeholder:text-neutral-400">
                            @error('safepay_api_key')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-neutral-700 mb-1.5">Secret Key</label>
                            <input type="password" name="safepay_secret_key" value="{{ $user->safepay_secret_key ?? '' }}"
                                   placeholder="sec_..."
                                   class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all placeholder:text-neutral-400">
                            @error('safepay_secret_key')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-neutral-700 mb-1.5">Webhook Secret</label>
                            <input type="password" name="safepay_webhook_secret" value="{{ $user->safepay_webhook_secret ?? '' }}"
                                   placeholder="whsec_..."
                                   class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all placeholder:text-neutral-400">
                            @error('safepay_webhook_secret')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-neutral-700 mb-1.5">Environment</label>
                            <select name="safepay_environment"
                                    class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                                <option value="sandbox" {{ ($user->safepay_environment ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                                <option value="live" {{ ($user->safepay_environment ?? '') === 'live' ? 'selected' : '' }}>Live (Production)</option>
                            </select>
                            @error('safepay_environment')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 active:scale-[0.98] transition-all shadow-lg shadow-emerald-500/25">
                            Save Safepay Keys
                        </button>
                    </div>
                </form>
            </div>

            {{-- How It Works --}}
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm p-6">
                <h3 class="font-black text-neutral-900 text-sm mb-4">How payments work</h3>
                <ol class="space-y-4">
                    @foreach([
                        ['icon' => '1', 'title' => 'You complete the order', 'desc' => 'Once all production stages are marked complete, the order is marked as Delivered.'],
                        ['icon' => '2', 'title' => 'Shop owner confirms receipt', 'desc' => 'The shop owner confirms they received the goods, triggering the payment step.'],
                        ['icon' => '3', 'title' => 'Shop owner chooses a secure method', 'desc' => 'They pay using your active Stripe or Safepay setup based on the checkout flow.'],
                        ['icon' => '4', 'title' => 'Payment recorded', 'desc' => 'The transaction is recorded and you see the payment in your Payments dashboard.'],
                    ] as $step)
                    <li class="flex items-start gap-4">
                        <div class="w-7 h-7 rounded-lg bg-red-50 text-red-600 text-xs font-black flex items-center justify-center shrink-0 mt-0.5">
                            {{ $step['icon'] }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-neutral-800">{{ $step['title'] }}</p>
                            <p class="text-xs text-neutral-400 mt-0.5">{{ $step['desc'] }}</p>
                        </div>
                    </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>

</div>

<script>
function switchPaymentTab(tab) {
    document.getElementById('panel-transactions').classList.toggle('hidden', tab !== 'transactions');
    document.getElementById('panel-methods').classList.toggle('hidden', tab !== 'methods');

    document.getElementById('tab-transactions').className = 'pb-4 text-sm font-bold border-b-2 transition-colors ' +
        (tab === 'transactions' ? 'text-primary-600 border-primary-600' : 'text-neutral-400 border-transparent hover:text-neutral-600');
    document.getElementById('tab-methods').className = 'pb-4 text-sm font-bold border-b-2 transition-colors ' +
        (tab === 'methods' ? 'text-primary-600 border-primary-600' : 'text-neutral-400 border-transparent hover:text-neutral-600');

    // Update URL without reload
    const url = new URL(window.location);
    if (tab === 'methods') {
        url.searchParams.set('tab', 'methods');
    } else {
        url.searchParams.delete('tab');
    }
    history.replaceState(null, '', url);
}
</script>
@endsection
