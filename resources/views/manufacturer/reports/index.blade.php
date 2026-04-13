@extends('layouts.app')

@section('title', 'Reports & Analytics - Manufacturer')
@section('page_title', 'Reports & Analytics')
@section('page_subtitle', 'Detailed financial and order insights')

@section('header_actions')
    <button class="inline-flex items-center gap-2 px-4 py-1.5 bg-white border border-neutral-200 rounded-lg text-sm font-bold text-neutral-600 hover:bg-neutral-50 transition-colors shadow-sm uppercase tracking-wider">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M21 15V19C21 19.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 19V15M7 10L12 15M12 15L17 10M12 15V3" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Export PDF
    </button>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Analytics KPI Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-6">
            <div class="w-12 h-12 bg-blue-600/10 text-blue-600 rounded-xl flex items-center justify-center">
                <div class="w-5 h-5 bg-blue-600 rounded-sm"></div>
            </div>
            <div>
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Total Revenue</p>
                <h3 class="text-xl font-bold text-neutral-900">₹{{ number_format($stats['totalRevenue']) }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-6">
            <div class="w-12 h-12 bg-orange-600/10 text-orange-600 rounded-xl flex items-center justify-center">
                <div class="w-5 h-5 border-2 border-orange-600 rounded-full"></div>
            </div>
            <div>
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Pending Receivables</p>
                <h3 class="text-xl font-bold text-neutral-900">₹{{ number_format($stats['pendingReceivables']) }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-6">
            <div class="w-12 h-12 bg-green-600/10 text-green-600 rounded-xl flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Orders Fulfilled</p>
                <h3 class="text-xl font-bold text-neutral-900">{{ $stats['ordersFulfilled'] }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-6 text-teal-600">
            <div class="w-12 h-12 bg-teal-600 text-white rounded-xl flex items-center justify-center text-[10px] font-bold">A</div>
            <div>
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Top Customer</p>
                <h3 class="text-xl font-bold text-neutral-900">{{ $stats['topCustomer'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Revenue Trends -->
        <div class="bg-white p-8 rounded-2xl border border-neutral-100 shadow-sm space-y-8">
            <h2 class="text-lg font-bold text-neutral-900 border-b border-neutral-50 pb-4">Revenue Trends</h2>
            <div class="h-64 flex items-end gap-4 px-4 border-b border-l border-neutral-100 pb-2 relative">
                @foreach($chartData['revenue']['labels'] as $index => $label)
                <div class="flex-1 flex flex-col items-center gap-2 group">
                    <div class="w-full bg-success-500/50 group-hover:bg-success-500 transition-colors rounded-t-sm" 
                         style="height: {{ $chartData['revenue']['data'][$index] / 2000 }}%"></div>
                    <span class="text-[9px] text-neutral-400 whitespace-nowrap">{{ $label }}</span>
                </div>
                @endforeach
                
                <div class="absolute bottom-[-40px] left-1/2 transform -translate-x-1/2 flex items-center gap-2">
                    <div class="w-3 h-3 bg-success-500 rounded-full"></div>
                    <span class="text-xs font-bold text-neutral-600">Total Revenue</span>
                </div>
            </div>
        </div>

        <!-- Order Status Distribution -->
        <div class="bg-white p-8 rounded-2xl border border-neutral-100 shadow-sm space-y-8">
            <h2 class="text-lg font-bold text-neutral-900 border-b border-neutral-50 pb-4">Order Status Distribution</h2>
            <div class="h-64 flex flex-col items-center justify-center relative">
                <div class="w-48 h-48 rounded-full border-[20px] border-neutral-100 flex items-center justify-center relative">
                    <div class="absolute inset-0 rounded-full border-[20px] border-transparent border-t-orange-500 border-r-orange-500 rotate-[-45deg]"></div>
                    <div class="absolute inset-0 rounded-full border-[20px] border-transparent border-l-primary-500 border-b-primary-500 rotate-[30deg]"></div>
                </div>
                
                <div class="absolute right-0 top-1/2 -translate-y-1/2 flex flex-col gap-3">
                    @php
                        $segments = [
                            ['label' => 'Pending', 'color' => 'bg-orange-500'],
                            ['label' => 'In Progress', 'color' => 'bg-primary-500'],
                            ['label' => 'Completed', 'color' => 'bg-success-500'],
                            ['label' => 'Rejected', 'color' => 'bg-error-500'],
                        ];
                    @endphp
                    @foreach($segments as $s)
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 {{ $s['color'] }} rounded-full"></div>
                        <span class="text-[11px] text-neutral-600 font-bold uppercase tracking-widest">{{ $s['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden mt-12">
        <div class="p-8 border-b border-neutral-50 bg-neutral-50/20 text-neutral-400 font-bold text-[13px] uppercase tracking-widest">
            Recent Transactions
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-primary-600 text-white">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Transaction ID</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Partner</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Method</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 italic">
                    @foreach($transactions as $trx)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-5 text-sm font-bold text-neutral-800">{{ $trx['id'] }}</td>
                        <td class="px-6 py-5 text-sm font-medium text-neutral-500">{{ $trx['date'] }}</td>
                        <td class="px-6 py-5 text-sm font-bold text-neutral-800">{{ $trx['partner'] }}</td>
                        <td class="px-6 py-5 text-xs text-neutral-400 font-bold">{{ $trx['method'] }}</td>
                        <td class="px-6 py-5 text-center">
                            @php
                                $statusClasses = [
                                    'Pending' => 'bg-orange-100 text-orange-700',
                                    'Paid' => 'bg-green-100 text-green-700'
                                ];
                                $class = $statusClasses[$trx['status']] ?? 'bg-neutral-100 text-neutral-600';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold {{ $class }} uppercase tracking-tighter">
                                {{ $trx['status'] }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-right font-bold text-neutral-900">₹{{ number_format($trx['amount']) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
