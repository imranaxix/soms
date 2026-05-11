@extends('layouts.app')

@section('content')
<main class="p-6 max-w-4xl mx-auto space-y-6">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-neutral-900 mb-2">Business Profile</h1>
            <p class="text-neutral-500 text-sm">View details and connect with this business.</p>
        </div>
        <a href="{{ url()->previous() }}" class="px-5 py-2.5 bg-white border border-neutral-200 text-neutral-700 rounded-lg text-sm font-bold hover:bg-neutral-50 transition">Go Back</a>
    </div>

    <!-- Profile Header Card -->
    <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden shadow-sm">
        <div class="p-8 flex items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 text-4xl font-bold shadow-inner">
                    {{ strtoupper(substr($user->business_name ?? $user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-neutral-900">{{ $user->business_name ?? $user->name }}</h2>
                    <p class="text-neutral-500 font-medium">{{ $user->email }}</p>
                    <div class="mt-3 inline-block px-3 py-1 bg-neutral-100 text-neutral-600 rounded-full text-xs font-bold uppercase tracking-wide">
                        {{ str_replace('_', ' ', $user->role) }}
                    </div>
                </div>
            </div>
            
            <form action="#" method="POST">
                @csrf
                <button type="button" onclick="alert('Hello world')" class="px-8 py-3.5 bg-primary-600 text-white font-bold rounded-lg hover:bg-primary-700 shadow-md shadow-primary-500/20 transition-all text-sm">
                    Send Connection Request
                </button>
            </form>
        </div>
    </div>
    
    <!-- Business Details -->
    <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden shadow-sm p-8">
        <h3 class="text-lg font-bold text-neutral-900 mb-6">Business Information</h3>
        <div class="grid grid-cols-2 gap-8">
            <div>
                <p class="text-sm font-bold text-neutral-400 uppercase tracking-wider mb-1">Contact Person</p>
                <p class="text-base font-semibold text-neutral-900">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-sm font-bold text-neutral-400 uppercase tracking-wider mb-1">Email</p>
                <p class="text-base font-semibold text-neutral-900">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-sm font-bold text-neutral-400 uppercase tracking-wider mb-1">Member Since</p>
                <p class="text-base font-semibold text-neutral-900">{{ $user->created_at->format('M Y') }}</p>
            </div>
        </div>
    </div>
</main>
@endsection
