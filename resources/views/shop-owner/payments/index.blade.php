@extends('layouts.app')

@section('title', 'Payments - SOMS')

@section('page_title', 'Payments')
@section('page_subtitle', 'Manage your outgoing payments')

@section('header_actions')
    <button onclick="toggleModal(true)" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition shadow-sm">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5V19M5 12H19" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Record Payment
    </button>
@endsection


    </div>

@section('content')

<!-- Stats Grid -->
    <div class="flex justify-between items-center mb-8 mt-4">
        <div class="bg-white rounded-xl pr-20 pl-10 py-6 flex  items-center  gap-4 shadow-sm border border-neutral-100">
            <div class="w-20 h-20 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 5H7C6.46957 5 5.96086 5.21071 5.58579 5.58579C5.21071 5.96086 5 6.46957 5 7V19C5 19.5304 5.21071 20.0391 5.58579 20.4142C5.96086 20.7893 6.46957 21 7 21H17C17.5304 21 18.0391 20.7893 18.4142 20.4142C18.7893 20.0391 19 19.5304 19 19V7C19 6.46957 18.7893 5.96086 18.4142 5.58579C18.0391 5.21071 17.5304 5 17 5H15M9 5C9 5.53043 9.21071 6.03914 9.58579 6.41421C9.96086 6.78929 10.4696 7 11 7H13C13.5304 7 14.0391 6.78929 14.4142 6.41421C14.7893 6.03914 15 5.53043 15 5M9 5C9 4.46957 9.21071 3.96086 9.58579 3.58579C9.96086 3.21071 10.4696 3 11 3H13C13.5304 3 14.0391 3.21071 14.4142 3.58579C14.7893 3.96086 15 4.46957 15 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div>
                <p class="text-md text-neutral-500">Total Order Value</p>
                <p class="text-3xl font-bold">RS 200,000</p>
            </div>
        </div>

        <div class="bg-white rounded-xl pr-20 pl-10 py-6 flex  items-center  gap-4 shadow-sm border border-neutral-100">
            <div class="w-20 h-20 bg-warning-100 text-warning-600 rounded-lg flex items-center justify-center">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div>
                <p class="text-md text-neutral-500">Pending Balance</p>
                <p class="text-3xl font-bold">RS 100,000</p>
            </div>
        </div>


        <div class="bg-white rounded-xl pr-20 pl-10 py-6 flex  items-center  gap-4 shadow-sm border border-neutral-100">
            <div class="w-20 h-20 bg-success-100 text-success-600 rounded-lg flex items-center justify-center">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div>
                <p class="text-md text-neutral-500">Tota Paid</p>
                <p class="text-3xl font-bold">RS 100,000</p>
            </div>
        </div>
    </div>


    <div class="flex items-center gap-8 mb-8 border-b border-neutral-200">
        <button onclick="switchTab('transactions')" id="transactionsTab" class="tab-btn active pb-4 text-sm font-semibold border-b-2 border-primary-600 text-primary-600 transition-all cursor-pointer">
            Transactions
        </button>
        <button onclick="switchTab('methods')" id="methodsTab" class="tab-btn pb-4 text-sm font-medium border-b-2 border-transparent text-neutral-500 hover:text-neutral-700 transition-all cursor-pointer">
            Payment Methods
        </button>
    </div>

    <div id="transactionsContent" class="tab-content block space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-neutral-100">
                <h2 class="text-lg font-semibold text-neutral-900">Recent Transactions</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead >
                        <tr class="bg-primary-600 border-b border-neutral-100 text-[13px] font-medium text-white uppercase tracking-wider">
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Order ID</th>
                            <th class="px-6 py-4">Paid To</th>
                            <th class="px-6 py-4 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($orders as $order)
                            @foreach($order['payments'] as $payment)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-neutral-500">{{ \Carbon\Carbon::parse($payment['date'])->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-primary-600">#{{ $order['id'] }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-neutral-800">{{ $order['manufacturerName'] }}</td>
                                <td class="px-6 py-4 text-sm font-extrabold text-success-600 text-right">₹{{ number_format($payment['amount']) }}</td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="methodsContent" class="tab-content hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-6 bg-white border border-neutral-100 rounded-2xl shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#ED1C24] rounded-xl flex items-center justify-center text-white font-bold italic">JC</div>
                    <div>
                        <p class="font-bold text-neutral-900">JazzCash</p>
                        <p class="text-xs text-neutral-400">Linked Wallet</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-success-600 bg-success-50 px-2 py-1 rounded">Active</span>
            </div>

            <div class="p-6 bg-white border border-neutral-100 rounded-2xl shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-neutral-900 rounded-xl flex items-center justify-center text-white">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                    </div>
                    <div>
                        <p class="font-bold text-neutral-900">Bank Transfer</p>
                        <p class="text-xs text-neutral-400">Direct IBFT</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-neutral-500 bg-neutral-50 px-2 py-1 rounded">Available</span>
            </div>
        </div>
    </div>

    <script>
        /**
         * Switches between the Transactions and Payment Methods tabs.
         */
        function switchTab(tab) {
            // Hide all tab content
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            
            // Reset all tab button styles
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-primary-600', 'text-primary-600', 'font-semibold', 'active');
                btn.classList.add('border-transparent', 'text-neutral-500', 'font-medium');
            });

            // Show selected tab content
            document.getElementById(tab + 'Content').classList.remove('hidden');
            
            // Highlight selected tab button
            const activeTab = document.getElementById(tab + 'Tab');
            activeTab.classList.add('border-primary-600', 'text-primary-600', 'font-semibold', 'active');
            activeTab.classList.remove('border-transparent', 'text-neutral-500', 'font-medium');
        }
    </script>
@endsection