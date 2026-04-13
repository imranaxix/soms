@extends('layouts.app')

@section('title', 'Payments - Manufacturer')
@section('page_title', 'Payments')
@section('page_subtitle', 'Track your incoming payments')

@section('header_actions')
    <select class="bg-white border border-neutral-200 rounded-lg px-4 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-500 shadow-sm">
        <option>All Time</option>
        <option>Last 30 Days</option>
        <option>Pending Only</option>
    </select>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Payment Tabs -->
    <div class="flex gap-8 border-b border-neutral-200">
        <button class="pb-4 text-sm font-bold text-primary-600 border-b-2 border-primary-600">Transactions</button>
        <button class="pb-4 text-sm font-bold text-neutral-400 hover:text-neutral-600 transition-colors">Payment Methods</button>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 py-2">
        <div class="bg-white px-6 py-8 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-6">
            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8C12 8.55228 11.5523 9 11 9C10.4477 9 10 8.55228 10 8C10 7.44772 10.4477 7 11 7C11.5523 7 12 7.44772 12 8Z" fill="currentColor"/>
                    <path d="M12 12C12 12.5523 11.5523 13 11 13C10.4477 13 10 12.5523 10 12C10 11.4477 10.4477 11 11 11C11.5523 11 12 11.4477 12 12Z" fill="currentColor"/>
                    <path d="M12 16C12 16.5523 11.5523 17 11 17C10.4477 17 10 16.5523 10 16C10 15.4477 10.4477 15 11 15C11.5523 15 12 15.4477 12 16Z" fill="currentColor"/>
                    <path d="M19 19C19 20.1046 18.1046 21 17 21H7C5.89543 21 5 20.1046 5 19V5C5 3.89543 5.89543 3 7 3H17C18.1046 3 19 3.89543 19 5V19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Total Order Value</p>
                <h3 class="text-2xl font-bold text-neutral-900">₹{{ number_format($stats['totalOrderValue']) }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-6">
            <div class="w-14 h-14 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Total Paid</p>
                <h3 class="text-2xl font-bold text-neutral-900">₹{{ number_format($stats['totalPaid']) }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-6">
            <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8V12L15 15M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Pending Balance</p>
                <h3 class="text-2xl font-bold text-neutral-900">₹{{ number_format($stats['pendingBalance']) }}</h3>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-neutral-50 bg-neutral-50/30">
            <h2 class="text-base font-bold text-neutral-900">Recent Transactions</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-primary-600 text-white">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Order ID</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Received From</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-center">Method</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach($transactions as $trx)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-5 text-sm font-medium text-neutral-600">{{ $trx['date'] }}</td>
                        <td class="px-6 py-5 text-sm font-bold text-neutral-900">{{ $trx['order_id'] }}</td>
                        <td class="px-6 py-5 text-sm text-neutral-600">{{ $trx['received_from'] }}</td>
                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex items-center px-3 py-1 bg-neutral-100 border border-neutral-200 rounded text-[10px] font-bold text-neutral-500 uppercase tracking-tighter italic">
                                {{ $trx['method'] }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-right font-bold text-success-600">₹{{ number_format($trx['amount']) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Balances -->
    <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-neutral-50 bg-neutral-50/30">
            <h2 class="text-base font-bold text-neutral-900">Order Balances</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-primary-600 text-white">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Order ID</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Product</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-right">Total Amount</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-right">Paid</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-right">Balance</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach($orderBalances as $item)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-5 text-sm font-bold text-neutral-900">{{ $item['order_id'] }}</td>
                        <td class="px-6 py-5 text-sm text-neutral-600">{{ $item['product'] }}</td>
                        <td class="px-6 py-5 text-right font-bold text-neutral-900">₹{{ number_format($item['total']) }}</td>
                        <td class="px-6 py-5 text-right font-bold text-success-600">₹{{ number_format($item['paid']) }}</td>
                        <td class="px-6 py-5 text-right font-bold text-orange-600">₹{{ number_format($item['balance']) }}</td>
                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700">
                                {{ $item['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
