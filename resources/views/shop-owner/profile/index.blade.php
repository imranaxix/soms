@extends('layouts.app')
@section('title', 'Profile - Shop Owner')
@section('page_title', 'My Profile')
@section('page_subtitle', 'Manage your account settings and business information.')
@section('header_actions')
    <a href="{{ route('shop.connections') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-neutral-100 text-neutral-700 rounded-lg font-medium hover:bg-neutral-200 transition-colors shadow-sm">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Back to Connections
    </a>
@endsection
@section('content')
<main class="p-6 max-w-4xl mx-auto space-y-6">

    <!-- Profile Header Card -->
    <div class="bg-white rounded-3xl border border-neutral-200 overflow-hidden shadow-sm relative mb-8">
        <!-- Cover Photo / Gradient -->
        <div class="h-32 bg-gradient-to-br from-primary-600 via-primary-500 to-indigo-600"></div>
        
        <div class="px-8 pb-8 pt-4 flex flex-col md:flex-row items-center md:items-end justify-between gap-6 relative -mt-16 md:-mt-12">
            <div class="flex flex-col md:flex-row items-center md:items-end gap-6 w-full md:w-auto">
                <div class="w-32 h-32 bg-white rounded-3xl p-1.5 shadow-xl relative shrink-0">
                    <div class="w-full h-full bg-primary-100 rounded-2xl flex items-center justify-center text-primary-600 text-5xl font-black">
                        {{ strtoupper(substr($user->business_name ?? $user->name, 0, 1)) }}
                    </div>
                </div>
                <div class="text-center md:text-left pb-1 mt-5 md:mt-0">
                    <h2 class="text-3xl font-black text-neutral-900 tracking-tight leading-tight">{{ $user->business_name ?? $user->name }}</h2>
                    <div class="flex flex-wrap justify-center md:justify-start items-center gap-3 mt-2">
                        <span class="inline-block px-3 py-1 bg-primary-50 text-primary-700 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-primary-100 shadow-sm">
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
                <h3 class="text-lg font-bold text-neutral-900">Business Profile Settings</h3>
                <p class="text-xs text-neutral-500 mt-0.5">Update your business details and contact information</p>
            </div>
            <div class="w-10 h-10 bg-white border border-neutral-200 rounded-xl flex items-center justify-center text-primary-600 shadow-sm">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            </div>
        </div>
        <div class="p-8">
            <form action="#" method="POST" class="space-y-8">
                <!-- Demo purposes, no real action yet -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Business Name</label>
                        <input type="text" class="w-full px-5 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all placeholder:text-neutral-300" value="{{ $user->business_name ?? 'My Awesome Shop' }}">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Contact Person</label>
                        <input type="text" class="w-full px-5 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all placeholder:text-neutral-300" value="{{ $user->name }}">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Email Address</label>
                        <input type="email" class="w-full px-5 py-3.5 bg-neutral-100 border border-neutral-200 rounded-xl text-sm font-bold text-neutral-400 cursor-not-allowed outline-none" value="{{ $user->email }}" disabled title="Email cannot be changed">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Phone Number</label>
                        <input type="text" class="w-full px-5 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all placeholder:text-neutral-300" placeholder="+92 300 1234567">
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest ml-1">Business Address</label>
                        <textarea rows="3" class="w-full px-5 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm font-bold text-neutral-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all placeholder:text-neutral-300" placeholder="123 Business Street, Karachi, Pakistan"></textarea>
                    </div>
                </div>

                <div class="pt-8 border-t border-dashed border-neutral-200 flex justify-end gap-4">
                    <button type="button" class="px-8 py-3.5 text-neutral-500 font-bold rounded-xl hover:bg-neutral-50 transition-colors text-sm">Discard Changes</button>
                    <button type="button" class="px-10 py-3.5 bg-primary-600 text-white font-black rounded-xl hover:bg-primary-700 shadow-xl shadow-primary-500/20 transition-all text-sm transform hover:-translate-y-0.5">Save Profile</button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection
