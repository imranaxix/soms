@extends('layouts.guest')

@section('title', 'SOMS - Supplier Order Management System')

@php
    $dashboardUrl = null;
    if (auth()->check()) {
        $routeName = match (auth()->user()->role) {
            'admin' => 'admin.dashboard',
            'manufacturer' => 'manufacturer.dashboard',
            default => 'shop.dashboard',
        };
        $dashboardUrl = \Illuminate\Support\Facades\Route::has($routeName)
            ? route($routeName)
            : route('notifications.index');
    }
@endphp

@section('content')
<style>
    html { scroll-behavior: smooth; }
    @keyframes float-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-14px); }
    }
    @keyframes fade-up {
        from { opacity: 0; transform: translateY(24px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-float-slow { animation: float-slow 7s ease-in-out infinite; }
    .fade-up-1 { animation: fade-up .7s ease-out both; }
    .fade-up-2 { animation: fade-up .7s ease-out .15s both; }
    .fade-up-3 { animation: fade-up .7s ease-out .3s both; }
</style>

{{-- ============ NAVBAR ============ --}}
<nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-lg border-b border-neutral-200/70">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 auth-gradient rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/25">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" class="text-white" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <span class="text-xl font-bold text-neutral-900 tracking-tight">SOMS</span>
            </a>

            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-neutral-600">
                <a href="#features" class="hover:text-primary-600 transition-colors">Features</a>
                <a href="#how-it-works" class="hover:text-primary-600 transition-colors">How It Works</a>
                <a href="#who-its-for" class="hover:text-primary-600 transition-colors">Who It's For</a>
            </div>

            <div class="flex items-center gap-3">
                @if(auth()->check())
                    <a href="{{ $dashboardUrl }}" class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-bold hover:bg-primary-700 active:scale-[0.98] transition-all shadow-lg shadow-primary-500/25">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-block px-5 py-2.5 text-sm font-bold text-neutral-700 hover:text-primary-600 transition-colors">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-bold hover:bg-primary-700 active:scale-[0.98] transition-all shadow-lg shadow-primary-500/25">
                        Get Started Free
                    </a>
                @endif
            </div>
        </div>
    </div>
</nav>

{{-- ============ HERO ============ --}}
<header class="relative overflow-hidden auth-gradient">
    <div class="absolute top-0 -left-32 w-[28rem] h-[28rem] bg-white/10 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-40 -right-24 w-[30rem] h-[30rem] bg-primary-400/20 rounded-full blur-3xl"></div>
    <div class="absolute top-1/3 right-1/4 w-72 h-72 bg-primary-300/10 rounded-full blur-3xl"></div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-8 pt-20 pb-24 lg:pt-28 lg:pb-32">
        <div class="max-w-3xl fade-up-1">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 glass-panel rounded-full text-primary-100 text-sm font-semibold mb-8">
                <span class="w-2 h-2 bg-success-500 rounded-full animate-pulse"></span>
                Supplier Order Management System
            </span>
            <h1 class="text-4xl md:text-6xl font-bold text-white leading-tight tracking-tight mb-6">
                From order to delivery,
                <span class="text-primary-200">perfectly in sync.</span>
            </h1>
            <p class="text-lg md:text-xl text-primary-100/90 font-medium leading-relaxed mb-10 max-w-2xl">
                SOMS connects shop owners with trusted manufacturers track every production stage,
                chat in real time, and pay securely.
            </p>
            <div class="flex flex-wrap items-center gap-4">
                @if(auth()->check())
                    <a href="{{ $dashboardUrl }}" class="px-8 py-4 bg-white text-primary-700 rounded-xl font-bold text-lg hover:bg-primary-50 active:scale-[0.98] transition-all shadow-2xl shadow-black/20">
                        Open Your Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-primary-700 rounded-xl font-bold text-lg hover:bg-primary-50 active:scale-[0.98] transition-all shadow-2xl shadow-black/20">
                        Create Free Account
                    </a>
                    <a href="#how-it-works" class="px-8 py-4 glass-panel text-white rounded-xl font-bold text-lg hover:bg-white/20 active:scale-[0.98] transition-all">
                        See How It Works
                    </a>
                @endif
            </div>
        </div>

        {{-- Product mockup --}}
        <div class="mt-16 lg:mt-20 max-w-4xl fade-up-3">
            <div class="glass-panel rounded-3xl p-4 sm:p-6 shadow-2xl shadow-black/30 animate-float-slow">
                <div class="bg-white rounded-2xl overflow-hidden shadow-xl">
                    {{-- Mock window bar --}}
                    <div class="flex items-center gap-2 px-5 py-3.5 border-b border-neutral-100 bg-neutral-50">
                        <span class="w-3 h-3 rounded-full bg-error-500"></span>
                        <span class="w-3 h-3 rounded-full bg-warning-500"></span>
                        <span class="w-3 h-3 rounded-full bg-success-500"></span>
                        <span class="ml-4 text-xs font-semibold text-neutral-400">soms.app / orders / #1042</span>
                    </div>
                    <div class="grid sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-neutral-100">
                        {{-- Order summary --}}
                        <div class="p-6">
                            <p class="text-xs font-bold uppercase tracking-wider text-neutral-400 mb-1">Order #1042</p>
                            <p class="text-lg font-bold text-neutral-900 leading-snug">Custom Embroidered Hoodies</p>
                            <p class="text-sm text-neutral-500 mt-1 font-medium">500 units · Stitched &amp; Printed</p>
                            <div class="mt-5 flex items-center justify-between bg-neutral-50 rounded-xl px-4 py-3">
                                <div>
                                    <p class="text-xs font-semibold text-neutral-400">Total Value</p>
                                    <p class="text-lg font-black text-neutral-900">$12,500</p>
                                </div>
                                <span class="px-3 py-1.5 bg-success-50 text-success-600 text-xs font-bold rounded-lg">Paid</span>
                            </div>
                            <div class="mt-4 flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full auth-gradient text-white text-xs font-bold flex items-center justify-center">SR</div>
                                <div class="w-8 h-8 rounded-full bg-neutral-800 text-white text-xs font-bold flex items-center justify-center -ml-3 ring-2 ring-white">MT</div>
                                <span class="text-xs font-semibold text-neutral-500 ml-1">Shop + Factory synced</span>
                            </div>
                        </div>
                        {{-- Production stages --}}
                        <div class="p-6">
                            <p class="text-xs font-bold uppercase tracking-wider text-neutral-400 mb-4">Production Stages</p>
                            <ul class="space-y-3.5">
                                <li class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-full bg-success-500 text-white flex items-center justify-center shrink-0">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                    <span class="text-sm font-semibold text-neutral-700">Fabric Sourcing</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-full bg-success-500 text-white flex items-center justify-center shrink-0">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                    <span class="text-sm font-semibold text-neutral-700">Cutting</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-full bg-primary-600 ring-4 ring-primary-100 text-white flex items-center justify-center shrink-0">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="4" fill="currentColor"/></svg>
                                    </span>
                                    <span class="text-sm font-bold text-neutral-900">Embroidery</span>
                                    <span class="ml-auto text-xs font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded-md">Live</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-full border-2 border-neutral-200 shrink-0"></span>
                                    <span class="text-sm font-medium text-neutral-400">Quality Check</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-full border-2 border-dashed border-neutral-200 shrink-0"></span>
                                    <span class="text-sm font-medium text-neutral-400">Shipment</span>
                                </li>
                            </ul>
                        </div>
                        {{-- Activity feed --}}
                        <div class="p-6">
                            <p class="text-xs font-bold uppercase tracking-wider text-neutral-400 mb-4">Recent Activity</p>
                            <ul class="space-y-4">
                                <li class="flex gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-neutral-800 leading-snug">New message from Manufacturer</p>
                                        <p class="text-xs text-neutral-400 font-medium mt-0.5">2 min ago</p>
                                    </div>
                                </li>
                                <li class="flex gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-success-50 text-success-600 flex items-center justify-center shrink-0">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" stroke="currentColor" stroke-width="2"/><path d="M1 10h22" stroke="currentColor" stroke-width="2"/></svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-neutral-800 leading-snug">Payment of $12,500 confirmed</p>
                                        <p class="text-xs text-neutral-400 font-medium mt-0.5">via Stripe · 1 hr ago</p>
                                    </div>
                                </li>
                                <li class="flex gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-warning-50 text-warning-600 flex items-center justify-center shrink-0">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-neutral-800 leading-snug">Embroidery stage completed</p>
                                        <p class="text-xs text-neutral-400 font-medium mt-0.5">Yesterday</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Wave divider --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 74" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full block" preserveAspectRatio="none">
            <path d="M0 74V37C240 0 480 0 720 18.5C960 37 1200 55.5 1440 46V74H0Z" fill="#ffffff"/>
        </svg>
    </div>
</header>

{{-- ============ STATS ============ --}}
<section class="bg-white py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <dl class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-10 text-center">
            <div class="bg-neutral-50 rounded-2xl px-6 py-8 border border-neutral-100">
                <dt class="text-3xl lg:text-4xl font-black text-neutral-900">Stage-by-stage</dt>
                <dd class="text-sm font-semibold text-neutral-500 mt-2">production visibility from sourcing to shipment</dd>
            </div>
            <div class="bg-neutral-50 rounded-2xl px-6 py-8 border border-neutral-100">
                <dt class="text-3xl lg:text-4xl font-black text-neutral-900">2 gateways</dt>
                <dd class="text-sm font-semibold text-neutral-500 mt-2">Stripe &amp; Safepay built in</dd>
            </div>
            <div class="bg-neutral-50 rounded-2xl px-6 py-8 border border-neutral-100">
                <dt class="text-3xl lg:text-4xl font-black text-neutral-900">Real-time</dt>
                <dd class="text-sm font-semibold text-neutral-500 mt-2">chat &amp; notifications between partners</dd>
            </div>
            <div class="bg-neutral-50 rounded-2xl px-6 py-8 border border-neutral-100">
                <dt class="text-3xl lg:text-4xl font-black text-neutral-900">Zero paperwork</dt>
                <dd class="text-sm font-semibold text-neutral-500 mt-2">every order digitized end to end</dd>
            </div>
        </dl>
    </div>
</section>

{{-- ============ FEATURES ============ --}}
<section id="features" class="bg-neutral-50 py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center mb-16">
            <span class="text-sm font-bold uppercase tracking-widest text-primary-600">Features</span>
            <h2 class="text-3xl md:text-4xl font-bold text-neutral-900 mt-3 tracking-tight">Everything your supply chain needs</h2>
            <p class="text-neutral-500 font-medium mt-4 text-lg">One platform to manage partners, production and payments — without the spreadsheets.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Feature cards --}}
            <div class="group bg-white rounded-2xl p-8 border border-neutral-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 auth-gradient rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-primary-500/25">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" class="text-white"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-lg font-bold text-neutral-900 mb-2">Production Stage Tracking</h3>
                <p class="text-neutral-500 text-sm font-medium leading-relaxed">Break orders into custom stages — sourcing, cutting, stitching, QC — and watch progress update live for both sides.</p>
            </div>

            <div class="group bg-white rounded-2xl p-8 border border-neutral-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 auth-gradient rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-primary-500/25">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" class="text-white"><rect x="1" y="4" width="22" height="16" rx="2" stroke="currentColor" stroke-width="2"/><path d="M1 10h22" stroke="currentColor" stroke-width="2"/></svg>
                </div>
                <h3 class="text-lg font-bold text-neutral-900 mb-2">Secure Integrated Payments</h3>
                <p class="text-neutral-500 text-sm font-medium leading-relaxed">Pay by card worldwide with Stripe Connect, or locally via Safepay funds flow straight to the manufacturer.</p>
            </div>

            <div class="group bg-white rounded-2xl p-8 border border-neutral-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 auth-gradient rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-primary-500/25">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" class="text-white"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-lg font-bold text-neutral-900 mb-2">Real-Time Chat</h3>
                <p class="text-neutral-500 text-sm font-medium leading-relaxed">Negotiate specs, share updates and resolve questions instantly with built-in messaging per connection.</p>
            </div>

            <div class="group bg-white rounded-2xl p-8 border border-neutral-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 auth-gradient rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-primary-500/25">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" class="text-white"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm14 10v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-lg font-bold text-neutral-900 mb-2">Partner Connections</h3>
                <p class="text-neutral-500 text-sm font-medium leading-relaxed">Discover manufacturers, send connection requests and build a private network of verified suppliers.</p>
            </div>

            <div class="group bg-white rounded-2xl p-8 border border-neutral-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 auth-gradient rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-primary-500/25">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" class="text-white"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-lg font-bold text-neutral-900 mb-2">Product Catalog</h3>
                <p class="text-neutral-500 text-sm font-medium leading-relaxed">Manufacturers publish products with variants and pricing; shop owners order directly from the catalog.</p>
            </div>

            <div class="group bg-white rounded-2xl p-8 border border-neutral-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 auth-gradient rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-primary-500/25">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" class="text-white"><path d="M18 20V10M12 20V4M6 20v-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-lg font-bold text-neutral-900 mb-2">Reports &amp; Insights</h3>
                <p class="text-neutral-500 text-sm font-medium leading-relaxed">Track spend, revenue and order performance with dashboards tailored to each role.</p>
            </div>
        </div>
    </div>
</section>

{{-- ============ HOW IT WORKS ============ --}}
<section id="how-it-works" class="bg-white py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center mb-16">
            <span class="text-sm font-bold uppercase tracking-widest text-primary-600">How It Works</span>
            <h2 class="text-3xl md:text-4xl font-bold text-neutral-900 mt-3 tracking-tight">Three steps to your first order</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-10 relative">
            <div class="hidden md:block absolute top-8 left-[16%] right-[16%] h-0.5 bg-gradient-to-r from-primary-100 via-primary-200 to-primary-100"></div>

            <div class="relative text-center px-4">
                <div class="relative z-10 w-16 h-16 mx-auto auth-gradient rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-lg shadow-primary-500/30 rotate-3">1</div>
                <h3 class="text-lg font-bold text-neutral-900 mt-6">Connect</h3>
                <p class="text-neutral-500 text-sm font-medium leading-relaxed mt-2">Create your account as a shop owner or manufacturer, then send a connection request to your partner.</p>
            </div>

            <div class="relative text-center px-4">
                <div class="relative z-10 w-16 h-16 mx-auto auth-gradient rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-lg shadow-primary-500/30 -rotate-3">2</div>
                <h3 class="text-lg font-bold text-neutral-900 mt-6">Order</h3>
                <p class="text-neutral-500 text-sm font-medium leading-relaxed mt-2">Pick products from the catalog or place a custom order — quantities, variants and pricing all agreed upfront.</p>
            </div>

            <div class="relative text-center px-4">
                <div class="relative z-10 w-16 h-16 mx-auto auth-gradient rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-lg shadow-primary-500/30 rotate-3">3</div>
                <h3 class="text-lg font-bold text-neutral-900 mt-6">Track &amp; Pay</h3>
                <p class="text-neutral-500 text-sm font-medium leading-relaxed mt-2">Follow each production stage in real time, then settle securely by card or mobile wallet on delivery.</p>
            </div>
        </div>
    </div>
</section>

{{-- ============ WHO IT'S FOR ============ --}}
<section id="who-its-for" class="bg-neutral-50 py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center mb-16">
            <span class="text-sm font-bold uppercase tracking-widest text-primary-600">Who It's For</span>
            <h2 class="text-3xl md:text-4xl font-bold text-neutral-900 mt-3 tracking-tight">Built for both sides of the deal</h2>
        </div>

        <div class="grid lg:grid-cols-2 gap-8 max-w-5xl mx-auto">
            {{-- Shop owners --}}
            <div class="bg-white rounded-3xl p-10 border border-neutral-100 shadow-sm hover:shadow-xl transition-shadow duration-300">
                <div class="w-14 h-14 bg-primary-50 rounded-2xl flex items-center justify-center mb-6">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" class="text-primary-600"><path d="M3 9l1-5h16l1 5M3 9v11a1 1 0 001 1h16a1 1 0 001-1V9M3 9h18M9 21v-6h6v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-2">For Shop Owners</h3>
                <p class="text-neutral-500 font-medium mb-8">Source from manufacturers you trust, with full visibility.</p>
                <ul class="space-y-4">
                    @foreach([
                        'Browse catalogs and compare real prices',
                        'Place custom orders with clear specifications',
                        'Watch production progress stage by stage',
                        'Pay safely by card, wallet or bank transfer',
                    ] as $point)
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-success-50 text-success-600 flex items-center justify-center shrink-0 mt-0.5">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="text-sm font-semibold text-neutral-700">{{ $point }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Manufacturers --}}
            <div class="bg-white rounded-3xl p-10 border border-neutral-100 shadow-sm hover:shadow-xl transition-shadow duration-300">
                <div class="w-14 h-14 bg-primary-50 rounded-2xl flex items-center justify-center mb-6">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" class="text-primary-600"><path d="M2 20h20M4 20V9l6 4V9l6 4V4h4v16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-neutral-900 mb-2">For Manufacturers</h3>
                <p class="text-neutral-500 font-medium mb-8">Win more orders and get paid on time, every time.</p>
                <ul class="space-y-4">
                    @foreach([
                        'Showcase products with variants & pricing',
                        'Accept or reject orders with one click',
                        'Update buyers by toggling production stages',
                        'Get paid directly via Stripe or Safepay',
                    ] as $point)
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-success-50 text-success-600 flex items-center justify-center shrink-0 mt-0.5">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="text-sm font-semibold text-neutral-700">{{ $point }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ============ CTA ============ --}}
<section class="bg-white py-20 lg:py-28">
    <div class="max-w-5xl mx-auto px-6 lg:px-8">
        <div class="relative overflow-hidden auth-gradient rounded-3xl px-8 py-16 lg:px-16 text-center shadow-2xl shadow-primary-500/30">
            <div class="absolute top-0 -left-16 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 -right-16 w-64 h-64 bg-primary-400/20 rounded-full blur-3xl"></div>
            <div class="relative">
                <h2 class="text-3xl md:text-4xl font-bold text-white tracking-tight mb-4">Ready to streamline your orders?</h2>
                <p class="text-primary-100 text-lg font-medium max-w-xl mx-auto mb-10">Join shops and factories already managing their production pipeline on SOMS.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    @if(auth()->check())
                        <a href="{{ $dashboardUrl }}" class="px-8 py-4 bg-white text-primary-700 rounded-xl font-bold text-lg hover:bg-primary-50 active:scale-[0.98] transition-all shadow-xl">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-primary-700 rounded-xl font-bold text-lg hover:bg-primary-50 active:scale-[0.98] transition-all shadow-xl">
                            Create Free Account
                        </a>
                        <a href="{{ route('login') }}" class="px-8 py-4 glass-panel text-white rounded-xl font-bold text-lg hover:bg-white/20 active:scale-[0.98] transition-all">
                            Sign In
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ FOOTER ============ --}}
<footer class="bg-neutral-900 py-14">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 auth-gradient rounded-lg flex items-center justify-center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="text-white" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-white">SOMS</span>
            </div>

            <div class="flex items-center gap-8 text-sm font-semibold text-neutral-400">
                <a href="#features" class="hover:text-white transition-colors">Features</a>
                <a href="#how-it-works" class="hover:text-white transition-colors">How It Works</a>
                <a href="{{ route('login') }}" class="hover:text-white transition-colors">Sign In</a>
                <a href="{{ route('register') }}" class="hover:text-white transition-colors">Register</a>
            </div>
        </div>
        <div class="border-t border-neutral-800 mt-10 pt-8 text-center">
            <p class="text-neutral-500 text-sm font-medium">&copy; 2026 SOMS Platform. All rights reserved.</p>
        </div>
    </div>
</footer>
@endsection
