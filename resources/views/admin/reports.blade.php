@extends('layouts.app')

@section('title', 'Reports - Admin')
@section('page_title', 'Reports')
@section('page_subtitle', 'Platform-wide financial reports')

@section('content')
    <!-- Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 mt-4">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-neutral-100">
            <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Order Value</p>
            <h3 class="text-2xl font-bold text-neutral-900">Rs {{ number_format($totalOrderValue) }}</h3>
            <p class="text-[11px] text-neutral-400 mt-1">{{ $totalOrders }} orders</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-neutral-100">
            <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Total Paid</p>
            <h3 class="text-2xl font-bold text-success-600">Rs {{ number_format($totalPaid) }}</h3>
            <p class="text-[11px] text-neutral-400 mt-1">Rs {{ number_format($paidAmount) }} confirmed payments</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-neutral-100">
            <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Pending Balance</p>
            <h3 class="text-2xl font-bold text-orange-600">Rs {{ number_format($pendingBalance) }}</h3>
            <p class="text-[11px] text-neutral-400 mt-1">Rs {{ number_format($pendingAmount) }} in-flight</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-neutral-100">
            <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-widest mb-1">Failed Payments</p>
            <h3 class="text-2xl font-bold text-red-600">Rs {{ number_format($failedAmount) }}</h3>
            <p class="text-[11px] text-neutral-400 mt-1">Across all providers</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Payment Method Breakdown -->
        <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-neutral-100">
                <h2 class="text-lg font-semibold text-neutral-900">Payment Method Breakdown</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[11px] font-medium text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                            <th class="px-6 py-3">Method</th>
                            <th class="px-6 py-3 text-center">Transactions</th>
                            <th class="px-6 py-3 text-right">Completed</th>
                            <th class="px-6 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($methodBreakdown as $row)
                        <tr>
                            <td class="px-6 py-4 text-sm font-semibold text-neutral-900 capitalize">{{ $row->method }}</td>
                            <td class="px-6 py-4 text-sm text-neutral-600 text-center">{{ $row->count }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-success-600 text-right">Rs {{ number_format($row->completed_amount) }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-neutral-900 text-right">Rs {{ number_format($row->total_amount) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-neutral-400">No payments yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Status Breakdown -->
        <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-neutral-100">
                <h2 class="text-lg font-semibold text-neutral-900">Payment Status Breakdown</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[11px] font-medium text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-center">Count</th>
                            <th class="px-6 py-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($statusBreakdown as $row)
                        <tr>
                            <td class="px-6 py-4 text-sm font-semibold capitalize {{ $row->status === 'completed' ? 'text-success-600' : ($row->status === 'failed' ? 'text-red-600' : 'text-orange-600') }}">
                                {{ $row->status }}
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-600 text-center">{{ $row->count }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-neutral-900 text-right">Rs {{ number_format($row->total_amount) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-sm text-neutral-400">No payments yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Monthly Trend -->
    <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-neutral-100">
            <h2 class="text-lg font-semibold text-neutral-900">Monthly Order Volume (Last 6 Months)</h2>
        </div>
        <div class="p-6">
            <div class="flex items-end gap-3 h-48">
                @foreach($monthlyLabels as $index => $label)
                @php
                    $max = $monthlyValue->max() ?: 1;
                    $height = max(4, round(($monthlyValue[$index] / $max) * 100));
                @endphp
                <div class="flex-1 flex flex-col items-center gap-2">
                    <span class="text-xs font-bold text-neutral-700">Rs {{ number_format($monthlyValue[$index] / 1000, $monthlyValue[$index] >= 1000 ? 0 : 1) }}k</span>
                    <div class="w-full bg-primary-100 rounded-t-lg" style="height: {{ $height }}%"></div>
                    <span class="text-[10px] text-neutral-400 font-medium">{{ $label }}</span>
                    <span class="text-[10px] text-neutral-400">{{ $monthlyOrders[$index] }} orders</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Top Manufacturers -->
    <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-neutral-100">
            <h2 class="text-lg font-semibold text-neutral-900">Top Manufacturers by Volume</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[11px] font-medium text-neutral-400 uppercase tracking-wider border-b border-neutral-100">
                        <th class="px-6 py-3">#</th>
                        <th class="px-6 py-3">Manufacturer</th>
                        <th class="px-6 py-3 text-center">Orders</th>
                        <th class="px-6 py-3 text-right">GMV</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($topManufacturers as $index => $m)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-neutral-400">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-neutral-900">{{ $m['name'] }}</td>
                        <td class="px-6 py-4 text-sm text-neutral-600 text-center">{{ $m['orders'] }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-primary-600 text-right">Rs {{ number_format($m['gmv']) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-sm text-neutral-400">No orders yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection