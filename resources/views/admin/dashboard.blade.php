@extends('layouts.app')

@section('title', 'Admin Dashboard - SOMS')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Platform overview')

@section('content')
    @if(session('success'))
        <div class="flex items-center gap-3 px-5 py-4 mb-6 bg-success-50 border border-success-200 text-success-700 rounded-xl text-sm font-medium">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 13.01 9 10.01"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-5 py-4 mb-6 bg-error-50 border border-error-200 text-error-700 rounded-xl text-sm font-medium">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 mt-4">
        <div class="bg-white rounded-xl p-6 flex items-center gap-4 shadow-sm border border-neutral-100">
            <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-neutral-500">Total Users</p>
                <p class="text-2xl font-bold">{{ $stats['users'] }}</p>
                <p class="text-[11px] text-neutral-400 mt-0.5">{{ $stats['shop_owners'] }} owners · {{ $stats['manufacturers'] }} manufacturers</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 flex items-center gap-4 shadow-sm border border-neutral-100">
            <div class="w-12 h-12 bg-warning-100 text-warning-600 rounded-lg flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 4H8C6.89543 4 6 4.89543 6 6V20C6 21.1046 6.89543 22 8 22H16C17.1046 22 18 21.1046 18 20V6C18 4.89543 17.1046 4 16 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-neutral-500">Orders</p>
                <p class="text-2xl font-bold">{{ $stats['orders'] }}</p>
                <p class="text-[11px] text-neutral-400 mt-0.5">{{ $stats['pending_orders'] }} pending</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 flex items-center gap-4 shadow-sm border border-neutral-100">
            <div class="w-12 h-12 bg-success-100 text-success-600 rounded-lg flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8V12L15 15M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-neutral-500">GMV</p>
                <p class="text-2xl font-bold">Rs {{ number_format($stats['gmv']) }}</p>
                <p class="text-[11px] text-neutral-400 mt-0.5">Rs {{ number_format($stats['collected']) }} collected</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 flex items-center gap-4 shadow-sm border border-neutral-100">
            <div class="w-12 h-12 bg-[#e0f2fe] text-[#0284c7] rounded-lg flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 10H21M7 15H8M12 15H13M6 19H18C19.1046 19 20 18.1046 20 17V7C20 5.89543 19.1046 5 18 5H6C4.89543 5 4 5.89543 4 7V17C4 18.1046 4.89543 19 6 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-neutral-500">Payments</p>
                <p class="text-2xl font-bold">{{ $stats['payments'] }}</p>
                <p class="text-[11px] text-neutral-400 mt-0.5">{{ $stats['pending_payments'] }} pending · {{ $stats['failed_payments'] }} failed</p>
            </div>
        </div>
    </div>

    <!-- Alerts Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <a href="{{ route('admin.users', ['status' => 'unverified']) }}" class="bg-white rounded-xl p-5 border border-neutral-100 shadow-sm flex items-center justify-between hover:border-warning-300 transition">
            <div>
                <p class="text-sm font-semibold text-neutral-800">Unverified Manufacturers</p>
                <p class="text-[11px] text-neutral-400 mt-0.5">Review and approve accounts</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-warning-100 text-warning-600 font-bold">{{ $stats['unverified'] }}</span>
        </a>
        <a href="{{ route('admin.users', ['status' => 'suspended']) }}" class="bg-white rounded-xl p-5 border border-neutral-100 shadow-sm flex items-center justify-between hover:border-error-300 transition">
            <div>
                <p class="text-sm font-semibold text-neutral-800">Suspended Accounts</p>
                <p class="text-[11px] text-neutral-400 mt-0.5">Currently blocked from login</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-error-100 text-error-600 font-bold">{{ $stats['suspended'] }}</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Users -->
        <div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-neutral-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-neutral-900">Recent Users</h2>
                <a href="{{ route('admin.users') }}" class="text-xs font-bold text-primary-600 hover:text-primary-800 uppercase tracking-wider">View All</a>
            </div>
            <div class="divide-y divide-neutral-100">
                @forelse($recentUsers as $user)
                <a href="{{ route('admin.users.show', $user->id) }}" class="flex items-center gap-4 px-6 py-4 hover:bg-neutral-50 transition">
                    <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center font-bold uppercase text-sm shrink-0">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-neutral-900 truncate">{{ $user->name }}</p>
                        <p class="text-xs text-neutral-400 truncate">{{ $user->email }}</p>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full {{ $user->role === 'manufacturer' ? 'bg-blue-50 text-blue-600' : ($user->role === 'admin' ? 'bg-neutral-100 text-neutral-600' : 'bg-green-50 text-green-600') }}">
                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                    </span>
                </a>
                @empty
                <div class="p-8 text-center text-sm text-neutral-400">No users yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-neutral-100">
                <h2 class="text-lg font-semibold text-neutral-900">Recent Payments</h2>
            </div>
            <div class="divide-y divide-neutral-100">
                @forelse($recentPayments as $payment)
                <div class="flex items-center gap-4 px-6 py-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-neutral-900">
                            Rs {{ number_format($payment->amount) }}
                            @if($payment->order)<span class="text-neutral-400 font-normal">· Order {{ $payment->order->order_number }}</span>@endif
                        </p>
                        <p class="text-xs text-neutral-400 truncate">
                            {{ $payment->payee->business_name ?? $payment->payee->name ?? '—' }} · {{ $payment->txn_ref_no }}
                        </p>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full
                        {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : ($payment->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700') }}">
                        {{ $payment->status }}
                    </span>
                </div>
                @empty
                <div class="p-8 text-center text-sm text-neutral-400">No payments yet.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection