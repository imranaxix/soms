@extends('layouts.app')

@section('title', 'Profile - Manufacturer')
@section('page_title', 'My Profile')
@section('page_subtitle', 'Manage your account settings and business information.')
@section('header_actions')
    <a href="{{ url()->previous(route('manufacturer.dashboard')) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-neutral-100 text-neutral-700 rounded-lg font-medium hover:bg-neutral-200 transition-colors shadow-sm">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Back
    </a>
@endsection
@section('content')
<main class="p-6 max-w-4xl mx-auto space-y-6">

    <!-- Profile Header Card -->
    <div class="bg-white rounded-3xl border border-neutral-200 overflow-hidden shadow-sm relative mb-8">
        <!-- Cover Photo / Gradient -->
        <div class="h-32 bg-gradient-to-br from-indigo-600 via-indigo-500 to-primary-600"></div>
        
        <div class="px-8 pb-8 pt-4 flex flex-col md:flex-row items-center md:items-end justify-between gap-6 relative -mt-16 md:-mt-12">
            <div class="flex flex-col md:flex-row items-center md:items-end gap-6 w-full md:w-auto">
                <div class="w-32 h-32 bg-white rounded-3xl p-1.5 shadow-xl relative shrink-0 group">
                    @if($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile" class="w-full h-full rounded-2xl object-cover">
                    @else
                        <div class="w-full h-full bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 text-5xl font-black">
                            {{ strtoupper(substr($user->business_name ?? $user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-black/50 rounded-2xl flex flex-col items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button type="button" onclick="document.getElementById('profile-image-input').click()" class="flex items-center gap-1.5 px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-lg text-white text-[11px] font-bold transition-colors">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            Replace
                        </button>
                        @if($user->profile_image)
                        <form action="{{ route('manufacturer.profile.image.delete') }}" method="POST" onsubmit="return confirm('Remove profile picture?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 bg-red-500/80 hover:bg-red-500 rounded-lg text-white text-[11px] font-bold transition-colors">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                Delete
                            </button>
                        </form>
                        @endif
                    </div>
                    <form action="{{ route('manufacturer.profile.image.upload') }}" method="POST" enctype="multipart/form-data" class="contents">
                        @csrf
                        <input type="file" id="profile-image-input" name="profile_image" accept="image/*" class="hidden" onchange="this.form.submit()">
                    </form>
                </div>
                <div class="text-center md:text-left pb-1 mt-5 md:mt-0">
                    <h2 class="text-3xl font-black text-neutral-900 tracking-tight leading-tight">{{ $user->business_name ?? $user->name }}</h2>
                    <div class="flex flex-wrap justify-center md:justify-start items-center gap-3 mt-2">
                        <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-indigo-100 shadow-sm">
                            {{ str_replace('_', ' ', $user->role) }}
                        </span>
                        <span class="text-xs text-neutral-500 font-medium italic">Member since {{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Account Details Form -->
    <div class="bg-white rounded-3xl border border-neutral-200 overflow-hidden shadow-sm">
        <div class="px-8 py-6 border-b border-neutral-100 bg-neutral-50/50 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-neutral-900">Manufacturing Business Settings</h3>
                <p class="text-xs text-neutral-500 mt-0.5">Manage your production facility details and contact info</p>
            </div>
            <div class="w-10 h-10 bg-white border border-neutral-200 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            </div>
        </div>
        <div class="p-8">
            <form action="{{ route('manufacturer.profile.update') }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Business Name</label>
                        <input type="text" name="business_name" class="w-full px-5 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all placeholder:text-neutral-300" value="{{ old('business_name', $user->business_name) }}">
                        @error('business_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Contact Person</label>
                        <input type="text" name="name" class="w-full px-5 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all placeholder:text-neutral-300" value="{{ old('name', $user->name) }}">
                        @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Email Address</label>
                        <input type="email" class="w-full px-5 py-3.5 bg-neutral-100 border border-neutral-200 rounded-xl text-sm font-bold text-neutral-400 cursor-not-allowed outline-none" value="{{ $user->email }}" disabled title="Email cannot be changed">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Phone Number</label>
                        <input type="text" class="w-full px-5 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all placeholder:text-neutral-300" placeholder="+92 300 1234567">
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Factory Address</label>
                        <textarea rows="3" class="w-full px-5 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all placeholder:text-neutral-300" placeholder="123 Industrial Area, Lahore, Pakistan"></textarea>
                    </div>
                </div>

                <div class="pt-8 border-t border-dashed border-neutral-200 flex justify-end gap-4">
                    <a href="{{ route('manufacturer.profile') }}" class="px-8 py-3.5 text-neutral-500 font-bold rounded-xl hover:bg-neutral-50 transition-colors text-sm">Discard Changes</a>
                    <button type="submit" class="px-10 py-3.5 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 shadow-xl shadow-indigo-500/20 transition-all text-sm transform hover:-translate-y-0.5">Save Profile</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="bg-white rounded-3xl border border-neutral-200 overflow-hidden shadow-sm">
        <div class="px-8 py-6 border-b border-neutral-100 bg-neutral-50/50 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-neutral-900">Payment Methods</h3>
                <p class="text-xs text-neutral-500 mt-0.5">Manage how you receive payments from Shop Owners</p>
            </div>
            <div class="w-10 h-10 bg-white border border-neutral-200 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="12" y1="17" x2="12" y2="17"/></svg>
            </div>
        </div>
        <div class="p-8 space-y-4">

            {{-- Stripe --}}
            <div class="flex items-center justify-between p-5 rounded-2xl border {{ $user->hasStripe() ? 'border-green-200 bg-green-50/40' : 'border-neutral-200 bg-neutral-50/40' }}">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 overflow-hidden">
                        <img src="{{ asset('stripe-logo.svg') }}" alt="Stripe" class="w-7 h-7 object-contain">
                    </div>
                    <div>
                        <p class="text-sm font-black text-neutral-900">Credit &amp; Debit Cards (Stripe)</p>
                        <p class="text-xs text-neutral-400 mt-0.5">Accept card payments worldwide</p>
                    </div>
                </div>
                @if($user->hasStripe())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 font-bold text-xs rounded-full border border-green-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-600 animate-pulse"></span>
                        Active
                    </span>
                @else
                    <a href="{{ route('manufacturer.payments.index', ['tab' => 'methods']) }}" class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-indigo-600 text-white font-bold text-xs rounded-full hover:bg-indigo-700 transition-colors shadow-sm">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Add Stripe
                    </a>
                @endif
            </div>

            {{-- Safepay --}}
            <div class="flex items-center justify-between p-5 rounded-2xl border {{ $user->hasSafepay() ? 'border-green-200 bg-green-50/40' : 'border-neutral-200 bg-neutral-50/40' }}">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 overflow-hidden">
                        <img src="{{ asset('safepay-logo.png') }}" alt="Safepay" class="w-7 h-7 object-contain">
                    </div>
                    <div>
                        <p class="text-sm font-black text-neutral-900">Safepay</p>
                        <p class="text-xs text-neutral-400 mt-0.5">Accept payments via Safepay across supported regions</p>
                    </div>
                </div>
                @if($user->hasSafepay())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 font-bold text-xs rounded-full border border-green-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-600 animate-pulse"></span>
                        Active
                    </span>
                @else
                    <a href="{{ route('manufacturer.payments.index', ['tab' => 'methods']) }}" class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-emerald-600 text-white font-bold text-xs rounded-full hover:bg-emerald-700 transition-colors shadow-sm">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Add Safepay
                    </a>
                @endif
            </div>

        </div>
    </div>
</main>
@endsection
