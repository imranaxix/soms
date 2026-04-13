@extends('layouts.app')

@section('title', 'My Orders - SOMS')

@section('page_title', 'My Orders')
@section('page_subtitle', 'Track and manage all your placed orders')

@section('header_actions')
    <a href="{{ route('shop.orders.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition shadow-sm">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        New Order
    </a>
@endsection

@section('content')

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 mt-4">
        <div class="bg-white rounded-xl p-6 flex items-center gap-4 shadow-sm border border-neutral-100">
            <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 5H7C6.46957 5 5.96086 5.21071 5.58579 5.58579C5.21071 5.96086 5 6.46957 5 7V19C5 19.5304 5.21071 20.0391 5.58579 20.4142C5.96086 20.7893 6.46957 21 7 21H17C17.5304 21 18.0391 20.7893 18.4142 20.4142C18.7893 20.0391 19 19.5304 19 19V7C19 6.46957 18.7893 5.96086 18.4142 5.58579C18.0391 5.21071 17.5304 5 17 5H15M9 5C9 5.53043 9.21071 6.03914 9.58579 6.41421C9.96086 6.78929 10.4696 7 11 7H13C13.5304 7 14.0391 6.78929 14.4142 6.41421C14.7893 6.03914 15 5.53043 15 5M9 5C9 4.46957 9.21071 3.96086 9.58579 3.58579C9.96086 3.21071 10.4696 3 11 3H13C13.5304 3 14.0391 3.21071 14.4142 3.58579C14.7893 3.96086 15 4.46957 15 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-neutral-500">Total Orders</p>
                <p class="text-2xl font-bold">3</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 flex items-center gap-4 shadow-sm border border-neutral-100">
            <div class="w-12 h-12 bg-warning-100 text-warning-600 rounded-lg flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-neutral-500">Pending</p>
                <p class="text-2xl font-bold">1</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 flex items-center gap-4 shadow-sm border border-neutral-100">
            <div class="w-12 h-12 bg-[#e0f2fe] text-[#0284c7] rounded-lg flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8V16M8 12H16M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-neutral-500">In Progress</p>
                <p class="text-2xl font-bold">1</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 flex items-center gap-4 shadow-sm border border-neutral-100">
            <div class="w-12 h-12 bg-success-100 text-success-600 rounded-lg flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-neutral-500">Completed</p>
                <p class="text-2xl font-bold">1</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-neutral-100 mb-8 mt-4 mx-0 mr-auto">
        <div class="flex flex-wrap gap-6 items-end">
            <div class="flex-1 min-w-50">
                <label class="block text-sm font-medium text-neutral-700 mb-1.5">Search</label>
                <input type="text" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition" placeholder="Search by product, manufacturer, order ID…">
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-neutral-700 mb-1.5">Filter by Status</label>
                <select class="w-full px-4 py-3 pr-14 bg-neutral-50 border border-neutral-200 rounded-lg text-sm outline-none hover:border-neutral-300" name="payment_terms">
                    <option value="All">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-neutral-700 mb-1.5">Sort By</label>
                <select class="w-full px-4 py-3 pr-14 bg-neutral-50 border border-neutral-200 rounded-lg text-sm outline-none hover:border-neutral-300" name="payment_terms">
                    <option value="date-desc">Newest First</option>
                    <option value="date-asc">Oldest First</option>
                    <option value="amount-desc">Highest Amount</option>
                    <option value="amount-asc">Lowest Amount</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b bg-primary-600 border-neutral-100 text-[13px] font-medium text-primary-50 uppercase tracking-wider">
                        <th class="px-6 py-4">Order ID</th>
                        <th class="px-6 py-4">Product</th>
                        <th class="px-6 py-4">Manufacturer</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                        <th class="px-6 py-4">Production</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    <!-- Mock data row 1 -->
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-semibold font-mono">
                            <a href="{{ route('shop.orders.show', 'ORD-001') }}" class="text-primary-600 hover:text-primary-700 hover:underline">#ORD-001</a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-neutral-900">Sweater - Sleeveless</div>
                            <div class="text-[11px] text-neutral-500 uppercase tracking-tighter">Qty: 100 pcs</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-700">XYZ Manufacturing</td>
                        <td class="px-6 py-4 text-sm text-neutral-500">Apr 11, 2026</td>
                        <td class="px-6 py-4 text-sm font-bold text-neutral-900 text-right">₹100,000</td>
                        <td class="px-6 py-4">
                            <div class="text-[10px] text-neutral-400 font-medium mb-1">Stitching phase</div>
                            <div class="w-24 h-1.5 bg-neutral-100 rounded-full overflow-hidden">
                                <div class="bg-primary-500 h-full" style="width: 65%"></div>
                            </div>
                            <div class="text-[10px] text-neutral-500 mt-0.5">65%</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-tight bg-primary-100 text-primary-600">In Progress</span>
                        </td>
                    </tr>
                    <!-- Mock data row 2 -->
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-semibold font-mono">
                            <a href="{{ route('shop.orders.show', 'ORD-002') }}" class="text-primary-600 hover:text-primary-700 hover:underline">#ORD-002</a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-neutral-900">Cotton T-Shirts</div>
                            <div class="text-[11px] text-neutral-500 uppercase tracking-tighter">Qty: 1200 pcs</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-700">Z-Fashion</td>
                        <td class="px-6 py-4 text-sm text-neutral-500">Apr 02, 2024</td>
                        <td class="px-6 py-4 text-sm font-bold text-neutral-900 text-right">₹2,40,000</td>
                        <td class="px-6 py-4">
                            <div class="text-[10px] text-neutral-400 font-medium mb-1 text-center">Awaiting Start</div>
                            <div class="w-24 h-1.5 bg-neutral-100 rounded-full overflow-hidden mx-0">
                                <div class="bg-primary-500 h-full" style="width: 0%"></div>
                            </div>
                            <div class="text-[10px] text-neutral-500 mt-0.5">0%</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-tight bg-warning-100 text-warning-600">Pending</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
