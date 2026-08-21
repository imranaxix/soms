@extends('layouts.app')

@section('title', 'Payment Methods - Manufacturer')
@section('page_title', 'Payment Methods')
@section('page_subtitle', 'Manage how shop owners can pay you.')

@section('header_actions')
    <a href="{{ route('manufacturer.profile') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-neutral-100 text-neutral-700 rounded-lg font-medium hover:bg-neutral-200 transition-colors shadow-sm text-sm">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 12H5M5 12L12 19M5 12L12 5"/>
        </svg>
        Back to Profile
    </a>
@endsection

@section('content')
<main class="p-6 max-w-3xl mx-auto space-y-6">

    {{-- Page Hero --}}
    <div class="rounded-2xl bg-gradient-to-br from-red-600 via-red-500 to-orange-500 p-6 text-white shadow-xl shadow-red-200 flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shrink-0">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <path d="M4 7.5A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5v9A2.5 2.5 0 0 1 17.5 19h-11A2.5 2.5 0 0 1 4 16.5v-9Z"/>
                <path d="M7 9h10"/>
                <path d="M7 13h6"/>
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-black tracking-tight">Payment Accounts</h2>
            <p class="text-sm text-white/80 mt-0.5">
                Shop owners will see your registered payment account when they pay for an order.
                Keep your details accurate to receive payments without delays.
            </p>
        </div>
    </div>

    {{-- Session Alerts --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-5 py-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-medium">
        <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Stripe Connect Card --}}
    <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
        {{-- Card Header --}}
        <div class="px-6 py-5 border-b border-neutral-100 bg-neutral-50/60 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background: linear-gradient(135deg, #635bff 0%, #00d4ff 100%);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
                        <rect x="2" y="5" width="20" height="14" rx="2"/>
                        <line x1="12" y1="17" x2="12" y2="17"/>
                        <path d="M12 11h.01"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-black text-neutral-900 text-sm">Credit & Debit Cards (Stripe)</h3>
                    <p class="text-xs text-neutral-400 mt-0.5">Receive card payments globally from Shop Owners directly into your bank account.</p>
                </div>
            </div>
            @if($user->hasStripe())
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 font-bold text-xs rounded-full border border-green-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-600 animate-pulse"></span>
                    Connected
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-neutral-100 text-neutral-500 font-bold text-xs rounded-full">
                    Disconnected
                </span>
            @endif
        </div>

        <div class="p-6 space-y-6">
            @if($user->hasStripe())
                <div class="bg-neutral-50 border border-neutral-200 rounded-2xl p-5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-neutral-800">Linked Stripe Account</p>
                        <p class="text-xs text-neutral-500 mt-0.5">Account ID: <code class="bg-neutral-200/60 px-2 py-0.5 rounded text-[11px] font-mono">{{ $user->stripe_connect_id }}</code></p>
                    </div>
                    <form action="{{ route('manufacturer.stripe.disconnect') }}" method="POST"
                          onsubmit="return confirm('Remove your Stripe integration? Shop owners will no longer be able to pay you using credit or debit cards.');">
                        @csrf
                        <button type="submit"
                                class="px-5 py-2.5 text-red-500 hover:text-red-600 font-bold text-sm rounded-xl hover:bg-red-50 transition-colors border border-transparent hover:border-red-200">
                            Disconnect Account
                        </button>
                    </form>
                </div>
            @else
                <div class="text-center py-6 max-w-md mx-auto space-y-4">
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-neutral-800">Set Up Online Card Payments</h4>
                        <p class="text-xs text-neutral-400 mt-1">Configure your Stripe merchant account in minutes to start accepting Visa, Mastercard, and other cards from Shop Owners.</p>
                    </div>
                    <a href="{{ route('manufacturer.stripe.connect') }}"
                       class="inline-block px-7 py-3 bg-gradient-to-r from-indigo-600 to-blue-500 hover:from-indigo-700 hover:to-blue-600 text-white font-black rounded-xl transition shadow-lg shadow-indigo-200 text-sm">
                        Connect with Stripe
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Other Methods Placeholder --}}
    <div class="bg-white rounded-2xl border border-dashed border-neutral-200 p-6 flex items-center gap-5 opacity-60">
        <div class="w-10 h-10 rounded-xl bg-neutral-100 flex items-center justify-center text-neutral-400 shrink-0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
        </div>
        <div>
            <p class="font-bold text-neutral-500 text-sm">More payment methods coming soon</p>
            <p class="text-xs text-neutral-400 mt-0.5">Easypaisa and bank transfers will be added in future updates.</p>
        </div>
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

</main>
@endsection
