@extends('layouts.app')

@section('title', 'Manufacturers - SOMS')

@section('page_title', 'Shop Owners')
@section('page_subtitle', 'Manage your connections and search for new retail partners.')

@section('content')
    <!-- Search Section -->
    <div class="bg-white p-8 rounded-xl shadow-sm border border-neutral-100 mb-8 mt-4">
        <h2 class="text-lg font-bold text-neutral-900 mb-6">Add New Connection</h2>
        <form action="#" method="GET" class="flex gap-3">
            <div class="flex-1 relative group">
                <input type="email" class="w-full pl-11 pr-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all group-hover:border-neutral-300" placeholder="Enter manufacturer's email (e.g., shop@demo.com)">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-hover:text-neutral-500 transition-colors" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <button type="submit" class="px-7 py-3.5 bg-primary-600 text-white rounded-lg font-bold hover:bg-primary-700 transition shadow-md shadow-primary-600/20">Find</button>
        </form>
    </div>

    <!-- Connections Section -->
    <div class="bg-white p-8 rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-lg font-bold text-neutral-900">My Connections</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Connection Card 1 -->
            <div class="p-6 rounded-xl border border-neutral-100 bg-neutral-50/50 hover:bg-white hover:border-primary-200 hover:shadow-md transition-all group">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 rounded-full bg-primary-600 from-primary-500 to-primary-600 text-white flex items-center justify-center text-xl font-bold shadow-sm">E</div>
                    <div>
                        <h3 class="text-base font-bold text-neutral-900 leading-tight">Elite Garments</h3>
                        <p class="text-xs text-neutral-500 mt-0.5">contact@elitegarments.com</p>
                    </div>
                </div>
                <button class="w-full py-2.5 bg-white border border-neutral-200 text-neutral-700 rounded-lg text-sm font-bold hover:bg-neutral-50 hover:border-neutral-300 transition group-hover:border-primary-200 group-hover:bg-primary-50">View Profile</button>
            </div>

            <!-- Connection Card 2 -->
            <div class="p-6 rounded-xl border border-neutral-100 bg-neutral-50/50 hover:bg-white hover:border-primary-200 hover:shadow-md transition-all group">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 rounded-full bg-primary-600 from-success-500 to-success-600 text-white flex items-center justify-center text-xl font-bold shadow-sm">Z</div>
                    <div>
                        <h3 class="text-base font-bold text-neutral-900 leading-tight">Z-Fashion</h3>
                        <p class="text-xs text-neutral-500 mt-1">info@zfashion.com</p>
                    </div>
                </div>
                <button class="w-full py-2.5  bg-white border border-neutral-200 text-neutral-700 rounded-lg text-sm font-bold hover:bg-neutral-50 hover:border-neutral-300 transition group-hover:border-primary-200 group-hover:bg-primary-50">View Profile</button>
            </div>
        </div>
    </div>
@endsection
