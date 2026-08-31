@extends('layouts.app')
@section('title', 'Business Profile - Manufacturer')
@section('page_title', 'Business Profile')
@section('page_subtitle', 'View details and connect with this business.')
@section('header_actions')
    <button onclick="history.back()" class="inline-flex items-center gap-2 px-4 py-2 bg-neutral-100 text-neutral-700 rounded-lg font-medium hover:bg-neutral-200 transition-colors shadow-sm">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Back
    </button>
@endsection
@section('content')
<main class="p-6 max-w-4xl mx-auto space-y-6">


    <!-- Profile Header Card -->
    <div class="bg-white rounded-3xl border border-neutral-200 shadow-sm relative">
        <!-- Cover Photo / Gradient -->
        <div class="h-32 bg-gradient-to-br from-indigo-600 via-indigo-500 to-primary-600"></div>
        
        <div class="px-8 pb-8 pt-4 flex flex-col md:flex-row items-center md:items-end justify-between gap-6 relative -mt-16 md:-mt-12">
            <div class="flex flex-col md:flex-row items-center md:items-end gap-6 w-full md:w-auto">
                <div class="w-32 h-32 bg-white rounded-3xl p-1.5 shadow-xl relative shrink-0">
                    @if($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->business_name ?? $user->name }}" class="w-full h-full rounded-2xl object-cover">
                    @else
                        <div class="w-full h-full bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 text-5xl font-black">
                            {{ strtoupper(substr($user->business_name ?? $user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="text-center md:text-left pb-1 mt-5 md:mt-0">
                    <h2 class="text-3xl font-black text-neutral-900 tracking-tight leading-tight">{{ $user->business_name ?? $user->name }}</h2>
                    <div class="flex flex-wrap justify-center md:justify-start items-center gap-3 mt-2">
                        <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-indigo-100 shadow-sm">
                            {{ str_replace('_', ' ', $user->role) }}
                        </span>
                        <span class="inline-block px-3 py-1 bg-success-50 text-success-700 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-success-100 shadow-sm">
                            Verified Shop
                        </span>
                    </div>
                </div>
            </div>
            
            @php
                $connection = auth()->user()->connections()->where(function($q) use ($user) {
                    $q->where('shop_owner_id', $user->id)->orWhere('manufacturer_id', $user->id);
                })->first();
            @endphp
            
            <div class="flex items-center gap-3">
            @if(!$connection || $connection->status === 'rejected')
                <form action="{{ route('connections.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="target_user_id" value="{{ $user->id }}">
                    <button type="submit" class="px-8 py-3.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/20 transition-all text-sm flex items-center gap-2">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5c-1.1 0-2 .9-2 2v2M16 3.13a4 4 0 0 1 0 7.75M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Connect Shop
                    </button>
                </form>
            @elseif($connection->status === 'pending')
                <button disabled class="px-8 py-3.5 bg-neutral-100 text-neutral-500 font-bold rounded-xl text-sm cursor-not-allowed border border-neutral-200 flex items-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"/><path d="M12 6V12L16 14"/></svg>
                    Request Pending
                </button>
            @elseif($connection->status === 'accepted')
                <button disabled class="px-6 py-3.5 bg-success-50 text-success-700 border border-success-200 font-bold rounded-xl text-sm cursor-not-allowed flex items-center gap-2 shadow-sm">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Connected
                </button>
                <div class="relative">
                    <button onclick="document.getElementById('profileDropdown').classList.toggle('hidden')" class="w-12 h-12 bg-white border border-neutral-200 text-neutral-600 rounded-xl flex items-center justify-center hover:bg-neutral-50 hover:border-neutral-300 transition shadow-sm focus:ring-2 focus:ring-indigo-500/20">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 12h.01M12 6h.01M12 18h.01" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white border border-neutral-200 rounded-xl shadow-xl z-50 overflow-hidden">
                        <form action="{{ route('connections.destroy', $connection->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this connection?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-left px-5 py-3.5 text-sm text-error-600 font-bold hover:bg-error-50 flex items-center gap-3 transition-colors">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 7L18.1327 19.1425C18.0579 20.1891 17.187 21 16.1378 21H7.86224C6.81296 21 5.94208 20.1891 5.86732 19.1425L5 7M10 11V17M14 11V17M15 7V4C15 3.44772 14.5523 3 14 3H10C9.44772 3 9 3.44772 9 4V7M4 7H20" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Remove Connection
                            </button>
                        </form>
                    </div>
                </div>
            @endif
            </div>
        </div>
    </div>
    
    <!-- Business Details -->
    <div class="bg-white rounded-3xl border border-neutral-200 overflow-hidden shadow-sm p-8 mt-6">
        <h3 class="text-xl font-black text-neutral-900 mb-8 flex items-center gap-3">
            <svg class="text-indigo-600" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
            Business Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-neutral-50 p-6 rounded-2xl border border-neutral-100 flex flex-col items-center text-center">
                <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-indigo-600 mb-4">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest mb-1">Contact Person</p>
                <p class="text-base font-bold text-neutral-900">{{ $user->name }}</p>
            </div>
            <div class="bg-neutral-50 p-6 rounded-2xl border border-neutral-100 flex flex-col items-center text-center">
                <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-indigo-600 mb-4">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                </div>
                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest mb-1">Email Address</p>
                <p class="text-base font-bold text-neutral-900 truncate w-full" title="{{ $user->email }}">{{ $user->email }}</p>
            </div>
            <div class="bg-neutral-50 p-6 rounded-2xl border border-neutral-100 flex flex-col items-center text-center">
                <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-indigo-600 mb-4">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest mb-1">Member Since</p>
                <p class="text-base font-bold text-neutral-900">{{ $user->created_at->format('M Y') }}</p>
            </div>
        </div>
    </div>

    <script>
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileDropdown');
            const button = event.target.closest('button[onclick]');
            if (dropdown && !dropdown.contains(event.target) && !button) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</main>
@endsection
