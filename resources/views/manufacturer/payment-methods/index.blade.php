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
            {{-- JazzCash-style icon --}}
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <path d="M2 10h20"/>
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

    {{-- JazzCash Card --}}
    <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">

        {{-- Card Header --}}
        <div class="px-6 py-5 border-b border-neutral-100 bg-neutral-50/60 flex items-center justify-between">
            <div class="flex items-center gap-3">
                {{-- JazzCash brand color dot --}}
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background: linear-gradient(135deg, #e8001a 0%, #ff6600 100%);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
                        <rect x="2" y="5" width="20" height="14" rx="2"/>
                        <path d="M2 10h20"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-black text-neutral-900 text-sm">JazzCash</h3>
                    <p class="text-[11px] text-neutral-400 mt-0.5">Mobile Wallet · Pakistan</p>
                </div>
            </div>
            {{-- Status Badge --}}
            @if($user->jazzcash_mobile)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-green-50 border border-green-200 text-green-700 text-[11px] font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                    Active
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-neutral-100 border border-neutral-200 text-neutral-500 text-[11px] font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-neutral-400 inline-block"></span>
                    Not Set Up
                </span>
            @endif
        </div>

        <div class="p-6">
            @if($user->jazzcash_mobile)
            {{-- Current Account Display --}}
            <div class="mb-6 rounded-xl border border-green-100 bg-green-50/60 p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest mb-1">Registered Account</p>
                    <p class="text-lg font-black text-neutral-900 tracking-tight">{{ $user->jazzcash_account_title }}</p>
                    <p class="text-sm text-neutral-500 font-medium mt-0.5">
                        {{ substr($user->jazzcash_mobile, 0, 4) }}•••••{{ substr($user->jazzcash_mobile, -3) }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 shrink-0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M20 6L9 17l-5-5"/>
                    </svg>
                </div>
            </div>

            {{-- Info note --}}
            <div class="mb-6 flex items-start gap-3 rounded-xl bg-blue-50 border border-blue-100 p-4 text-xs text-blue-700">
                <svg class="shrink-0 mt-0.5" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>Your JazzCash number is only shown (partially masked) to shop owners at the time of payment. It is never publicly displayed on your profile.</span>
            </div>
            @endif

            {{-- Add / Update Form --}}
            <form action="{{ route('manufacturer.payment-methods.jazzcash.save') }}" method="POST" class="space-y-5" id="jazzcash-form">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label for="jazzcash_mobile" class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest ml-1">
                            JazzCash Mobile Number
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400 text-sm font-bold pointer-events-none">
                                🇵🇰
                            </span>
                            <input
                                id="jazzcash_mobile"
                                type="tel"
                                name="jazzcash_mobile"
                                value="{{ old('jazzcash_mobile', $user->jazzcash_mobile) }}"
                                placeholder="03001234567"
                                maxlength="11"
                                class="w-full pl-10 pr-4 py-3.5 bg-neutral-50 border @error('jazzcash_mobile') border-red-400 bg-red-50 @else border-neutral-200 @enderror rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all placeholder:text-neutral-300 placeholder:font-normal"
                            >
                        </div>
                        @error('jazzcash_mobile')
                            <p class="text-xs text-red-500 ml-1 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-[11px] text-neutral-400 ml-1">Format: 03XXXXXXXXX (11 digits)</p>
                    </div>

                    <div class="space-y-2">
                        <label for="jazzcash_account_title" class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest ml-1">
                            Account Title / Name
                        </label>
                        <input
                            id="jazzcash_account_title"
                            type="text"
                            name="jazzcash_account_title"
                            value="{{ old('jazzcash_account_title', $user->jazzcash_account_title) }}"
                            placeholder="e.g. Ali Textiles Manufacturing"
                            class="w-full px-4 py-3.5 bg-neutral-50 border @error('jazzcash_account_title') border-red-400 bg-red-50 @else border-neutral-200 @enderror rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all placeholder:text-neutral-300 placeholder:font-normal"
                        >
                        @error('jazzcash_account_title')
                            <p class="text-xs text-red-500 ml-1 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-[11px] text-neutral-400 ml-1">This name will be shown to the shop owner when paying.</p>
                    </div>
                </div>

                {{-- Collapsible Advanced Settings Drawer --}}
                <div class="border border-neutral-200 rounded-xl overflow-hidden bg-neutral-50/50">
                    <button type="button" 
                            onclick="document.getElementById('advanced-settings').classList.toggle('hidden'); document.getElementById('arrow-icon').classList.toggle('rotate-180');"
                            class="w-full px-5 py-4 flex items-center justify-between text-left font-bold text-neutral-700 hover:bg-neutral-100/50 transition-colors">
                        <div class="flex items-center gap-2">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <span>Advanced Developer / Direct Payment Settings</span>
                        </div>
                        <svg id="arrow-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="transition-transform duration-200 @if(!$errors->has('jazzcash_merchant_id') && !$errors->has('jazzcash_password') && !$errors->has('jazzcash_integrity_salt')) @else rotate-180 @endif">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>
                    <div id="advanced-settings" class="px-5 pb-5 pt-2 border-t border-neutral-100 bg-white space-y-4 @if(!$errors->has('jazzcash_merchant_id') && !$errors->has('jazzcash_password') && !$errors->has('jazzcash_integrity_salt')) hidden @endif">
                        <p class="text-xs text-neutral-500 mb-3">
                            Provide your custom JazzCash Sandbox/Production Merchant credentials to receive funds directly into your merchant balance. Leave blank to use default platform processing.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-2">
                                <label for="jazzcash_merchant_id" class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest ml-1">
                                    Merchant ID
                                </label>
                                <input
                                    id="jazzcash_merchant_id"
                                    type="text"
                                    name="jazzcash_merchant_id"
                                    value="{{ old('jazzcash_merchant_id', $user->jazzcash_merchant_id) }}"
                                    placeholder="MC_XXXXXX"
                                    class="w-full px-4 py-3 bg-neutral-50 border @error('jazzcash_merchant_id') border-red-400 bg-red-50 @else border-neutral-200 @enderror rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all placeholder:text-neutral-300 placeholder:font-normal"
                                >
                                @error('jazzcash_merchant_id')
                                    <p class="text-xs text-red-500 ml-1 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="jazzcash_password" class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest ml-1">
                                    API Password
                                </label>
                                <input
                                    id="jazzcash_password"
                                    type="password"
                                    name="jazzcash_password"
                                    value="{{ old('jazzcash_password', $user->jazzcash_password) }}"
                                    placeholder="••••••••"
                                    class="w-full px-4 py-3 bg-neutral-50 border @error('jazzcash_password') border-red-400 bg-red-50 @else border-neutral-200 @enderror rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all placeholder:text-neutral-300 placeholder:font-normal"
                                >
                                @error('jazzcash_password')
                                    <p class="text-xs text-red-500 ml-1 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="jazzcash_integrity_salt" class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest ml-1">
                                    Integrity Salt
                                </label>
                                <input
                                    id="jazzcash_integrity_salt"
                                    type="password"
                                    name="jazzcash_integrity_salt"
                                    value="{{ old('jazzcash_integrity_salt', $user->jazzcash_integrity_salt) }}"
                                    placeholder="••••••••"
                                    class="w-full px-4 py-3 bg-neutral-50 border @error('jazzcash_integrity_salt') border-red-400 bg-red-50 @else border-neutral-200 @enderror rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all placeholder:text-neutral-300 placeholder:font-normal"
                                >
                                @error('jazzcash_integrity_salt')
                                    <p class="text-xs text-red-500 ml-1 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-dashed border-neutral-200">
                    <div class="flex items-center gap-2 text-xs text-neutral-400">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Secured & encrypted
                    </div>
                    <div class="flex gap-3">
                        @if($user->jazzcash_mobile)
                        <form action="{{ route('manufacturer.payment-methods.jazzcash.remove') }}" method="POST"
                              onsubmit="return confirm('Remove your JazzCash account? Shop owners will no longer be able to pay you until you add a new one.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-5 py-2.5 text-red-500 hover:text-red-600 font-bold text-sm rounded-xl hover:bg-red-50 transition-colors border border-transparent hover:border-red-200">
                                Remove
                            </button>
                        </form>
                        @endif
                        <button type="submit" form="jazzcash-form"
                                class="px-7 py-2.5 bg-gradient-to-r from-red-600 to-orange-500 hover:from-red-700 hover:to-orange-600 text-white font-black rounded-xl transition shadow-lg shadow-red-200 text-sm">
                            {{ $user->jazzcash_mobile ? 'Update Account' : 'Save Account' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                ['icon' => '3', 'title' => 'Shop owner pays via JazzCash', 'desc' => 'They enter the amount (based on payment terms), and pay directly to your JazzCash number shown here.'],
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
