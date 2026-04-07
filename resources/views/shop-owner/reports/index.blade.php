@extends('layouts.app')

@section('title', 'Reports & Analytics - SOMS')

@section('page_title', 'Reports & Analytics')
@section('page_subtitle', 'Detailed financial and order insights')

@section('header_actions')
    <button class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-neutral-200 text-neutral-700 rounded-lg text-sm font-medium hover:bg-neutral-50 hover:border-neutral-300 transition shadow-sm">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 16V17C4 18.1046 4.89543 19 6 19H18C19.1046 19 20 18.1046 20 17V16M16 12L12 16M12 16L8 12M12 16V4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Export PDF
    </button>
@endsection

@section('content')
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 mt-4">
        <div class="bg-white rounded-xl p-6 flex items-center gap-4 shadow-sm border border-neutral-100">
            <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center shrink-0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Total Spend</p>
                <p class="text-2xl font-bold text-neutral-900 leading-none">₹1,55,000</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 flex items-center gap-4 shadow-sm border border-neutral-100">
            <div class="w-12 h-12 bg-warning-100 text-warning-600 rounded-lg flex items-center justify-center shrink-0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Pending Liabilities</p>
                <p class="text-2xl font-bold text-neutral-900 leading-none">₹3,15,000</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 flex items-center gap-4 shadow-sm border border-neutral-100">
            <div class="w-12 h-12 bg-success-100 text-success-600 rounded-lg flex items-center justify-center shrink-0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 13L9 17L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Orders Fulfilled</p>
                <p class="text-2xl font-bold text-neutral-900 leading-none">1</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 flex items-center gap-4 shadow-sm border border-neutral-100 leading-none">
            <div class="w-12 h-12 bg-sky-100 text-sky-600 rounded-lg flex items-center justify-center shrink-0">
                <div class="w-6 h-6 rounded-full bg-linear-to-br from-sky-400 to-sky-600 text-white flex items-center justify-center text-[10px] font-bold">E</div>
            </div>
            <div>
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider mb-1">Top Manufacturer</p>
                <p class="text-lg font-bold text-neutral-900">Elite Garments</p>
            </div>
        </div>
    </div>

    <!-- Main Charts Placeholder -->
    <div class="bg-white rounded-xl shadow-sm border border-neutral-100 p-8 mb-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-lg font-bold text-neutral-900 leading-none">Order Analytics</h2>
        </div>
        <div class="h-80 bg-neutral-50/50 border-2 border-dashed border-neutral-200 rounded-xl flex items-center justify-center">
            <div class="text-center group cursor-default">
                <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center mx-auto mb-4 border border-neutral-100 group-hover:scale-110 group-hover:border-primary-100 transition-transform">
                    
                </div>
                <p class="text-sm font-semibold text-neutral-600 uppercase tracking-widest">Analytics Charts Area</p>
                <p class="text-xs text-neutral-400 mt-2">Interactive data visualizations will be rendered here</p>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-neutral-100">
            <h2 class="text-lg font-bold text-neutral-900 leading-none">Recent Transactions</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-neutral-100 text-[13px] font-medium text-neutral-500 uppercase tracking-wider">
                        <th class="px-6 py-4">Transaction ID</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Partner</th>
                        <th class="px-6 py-4">Method</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-semibold text-primary-600 font-mono">TRX-A8F2K</td>
                        <td class="px-6 py-4 text-sm text-neutral-500">Apr 04, 2024</td>
                        <td class="px-6 py-4 text-sm font-medium text-neutral-800">Elite Garments</td>
                        <td class="px-6 py-4 text-sm text-neutral-600">Bank Transfer</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-tight bg-success-100 text-success-600">Paid</span>
                        </td>
                        <td class="px-6 py-4 text-sm font-extrabold text-neutral-900 text-right">₹50,000</td>
                    </tr>
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-semibold text-primary-600 font-mono">PEND-ORD1</td>
                        <td class="px-6 py-4 text-sm text-neutral-500">May 15, 2024</td>
                        <td class="px-6 py-4 text-sm font-medium text-neutral-800">Elite Garments</td>
                        <td class="px-6 py-4 text-sm text-neutral-600">—</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-tight bg-warning-100 text-warning-600">Pending</span>
                        </td>
                        <td class="px-6 py-4 text-sm font-extrabold text-neutral-900 text-right">₹75,000</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
