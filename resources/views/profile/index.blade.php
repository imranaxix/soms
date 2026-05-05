@extends('layouts.app')

@section('content')
<main class="p-6 max-w-4xl mx-auto space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-black tracking-tight text-neutral-900 mb-2">My Profile</h1>
        <p class="text-neutral-500 text-sm">Manage your account settings and business information.</p>
    </div>

    <!-- Profile Header Card -->
    <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden shadow-sm">
        <div class="p-8 flex items-center gap-6">
            <div class="w-24 h-24 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 text-4xl font-bold">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold text-neutral-900">{{ $user->name }}</h2>
                <p class="text-neutral-500 font-medium">{{ $user->email }}</p>
                <div class="mt-3 inline-block px-3 py-1 bg-neutral-100 text-neutral-600 rounded-full text-xs font-bold uppercase tracking-wide">
                    {{ str_replace('_', ' ', $user->role) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Account Details Form -->
    <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-neutral-100">
            <h3 class="text-lg font-bold text-neutral-900">Account Details</h3>
        </div>
        <div class="p-6">
            <form action="#" method="POST" class="space-y-6">
                <!-- Demo purposes, no real action yet -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-neutral-700 mb-2">Full Name</label>
                        <input type="text" class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-colors" value="{{ $user->name }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-neutral-700 mb-2">Email Address</label>
                        <input type="email" class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-colors" value="{{ $user->email }}" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-neutral-700 mb-2">Business Name</label>
                        <input type="text" class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-colors" value="Demo Business Ltd">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-neutral-700 mb-2">Phone Number</label>
                        <input type="text" class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-colors" placeholder="+1 (555) 000-0000">
                    </div>
                     <div>
                        <label class="block text-sm font-semibold text-neutral-700 mb-2">Address</label>
                        <input type="text" class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-colors" placeholder="123 Main St, Anytown, USA">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-neutral-700 mb-2">Website</label>
                        <input type="text" class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-colors" placeholder="www.example.com">
                    </div>
                </div>

                <div class="pt-6 border-t border-dashed border-neutral-200 flex justify-end gap-3">
                    <button type="button" class="px-6 py-2.5 border border-neutral-200 text-neutral-600 font-semibold rounded-lg hover:bg-neutral-50 transition-colors text-sm">Cancel</button>
                    <button type="button" class="px-6 py-2.5 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 shadow-md shadow-primary-500/20 transition-all text-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection
