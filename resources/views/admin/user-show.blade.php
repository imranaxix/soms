@extends('layouts.app')

@section('title', $user->name . ' - Admin')
@section('back_link', route('admin.users'))
@section('page_title', $user->name)
@section('page_subtitle', $user->email)

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-4">
        <!-- User Info Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center font-bold uppercase text-lg shrink-0">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-neutral-900">{{ $user->name }}</h3>
                        <p class="text-xs text-neutral-400">{{ $user->business_name ?? 'No business name' }}</p>
                    </div>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-neutral-400">Role</span>
                        <span class="font-semibold text-neutral-800 capitalize">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-400">Email</span>
                        <span class="font-semibold text-neutral-800">{{ $user->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-400">Joined</span>
                        <span class="font-semibold text-neutral-800">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-neutral-400">Account</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $user->is_active ? 'Active' : 'Suspended' }}
                        </span>
                    </div>
                    @if($user->role === 'manufacturer')
                    <div class="flex justify-between items-center">
                        <span class="text-neutral-400">Verification</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $user->is_verified ? 'bg-blue-100 text-blue-600' : 'bg-warning-100 text-warning-600' }}">
                            {{ $user->is_verified ? 'Verified' : 'Unverified' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-400">Stripe</span>
                        <span class="font-semibold {{ $user->hasStripe() ? 'text-success-600' : 'text-neutral-400' }}">{{ $user->hasStripe() ? 'Connected' : 'Not connected' }}</span>
                    </div>
                    @endif
                </div>
                @if($user->role !== 'admin')
                <div class="mt-6 space-y-3">
                    <form action="{{ route('admin.users.toggle-active', $user->id) }}" method="POST" onsubmit="return confirm('{{ $user->is_active ? 'Suspend' : 'Activate' }} {{ $user->name }}?');">
                        @csrf
                        <button type="submit" class="w-full py-3 rounded-xl font-bold text-sm transition {{ $user->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-600 text-white hover:bg-green-700' }}">
                            {{ $user->is_active ? 'Suspend Account' : 'Activate Account' }}
                        </button>
                    </form>
                    @if($user->role === 'manufacturer')
                    <form action="{{ route('admin.users.toggle-verified', $user->id) }}" method="POST" onsubmit="return confirm('{{ $user->is_verified ? 'Unverify' : 'Verify' }} {{ $user->name }}?');">
                        @csrf
                        <button type="submit" class="w-full py-3 rounded-xl font-bold text-sm transition {{ $user->is_verified ? 'bg-warning-50 text-warning-600 hover:bg-warning-100' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                            {{ $user->is_verified ? 'Mark as Unverified' : 'Verify Account' }}
                        </button>
                    </form>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Orders & Payments -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h2 class="text-lg font-semibold text-neutral-900">Recent Orders</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[11px] font-medium text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                                <th class="px-6 py-3">Order #</th>
                                <th class="px-6 py-3">Product</th>
                                <th class="px-6 py-3 text-right">Total</th>
                                <th class="px-6 py-3 text-right">Paid</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($orders as $order)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-semibold text-primary-600">{{ $order->order_number }}</td>
                                <td class="px-6 py-4 text-sm text-neutral-800">{{ $order->product->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-neutral-900 text-right">Rs {{ number_format($order->total_amount) }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-success-600 text-right">Rs {{ number_format($order->paid_amount) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full {{ $order->status === 'Completed' ? 'bg-green-100 text-green-700' : ($order->status === 'Cancelled' || $order->status === 'Rejected' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700') }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-neutral-400">No orders yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-neutral-100">
                    <h2 class="text-lg font-semibold text-neutral-900">Recent Payments</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[11px] font-medium text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                                <th class="px-6 py-3">Amount</th>
                                <th class="px-6 py-3">Order</th>
                                <th class="px-6 py-3">Reference</th>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($payments as $payment)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-bold text-neutral-900">Rs {{ number_format($payment->amount) }}</td>
                                <td class="px-6 py-4 text-sm text-neutral-600">{{ $payment->order->order_number ?? '—' }}</td>
                                <td class="px-6 py-4 text-xs text-neutral-400 font-mono">{{ $payment->txn_ref_no }}</td>
                                <td class="px-6 py-4 text-sm text-neutral-500">{{ $payment->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : ($payment->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700') }}">
                                        {{ $payment->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-neutral-400">No payments yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection