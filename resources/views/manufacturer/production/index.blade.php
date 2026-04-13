@extends('layouts.app')

@section('title', 'Production Management - Manufacturer')
@section('page_title', 'Production Management')
@section('page_subtitle', 'Track and update production stages')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    @foreach($orders as $order)
    <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm p-8 space-y-8">
        <!-- Order Header -->
        <div class="flex justify-between items-start">
            <div class="space-y-1">
                <h3 class="text-xl font-bold text-neutral-900 border-b border-neutral-100 pb-2 mb-2">Order {{ $order['id'] }}</h3>
                <p class="text-sm text-neutral-500 font-medium">{{ $order['product'] }} - {{ $order['quantity'] }}</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-600 mt-1">
                    {{ $order['progress'] }}%
                </span>
            </div>
        </div>

        <!-- Quick Stats Row -->
        <div class="grid grid-cols-3 gap-4 bg-neutral-50 p-4 rounded-xl border border-neutral-100">
            <div>
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-wider mb-1">Shop Owner</p>
                <p class="text-sm font-bold text-neutral-800">{{ $order['shop_owner'] }}</p>
            </div>
            <div>
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-wider mb-1">Due Date</p>
                <p class="text-sm font-bold text-neutral-800">{{ $order['due_date'] }}</p>
            </div>
            <div>
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-wider mb-1">Current Stage</p>
                <p class="text-sm font-bold text-neutral-800">{{ $order['current_stage'] }}</p>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="w-full h-2 bg-neutral-100 rounded-full overflow-hidden">
            <div class="h-full bg-primary-500 transition-all duration-1000" style="width: {{ $order['progress'] }}%"></div>
        </div>

        <!-- Production Stages List -->
        <div class="space-y-4">
            @foreach($order['stages'] as $stage)
            <div class="relative flex items-center gap-4 p-4 rounded-xl border {{ $stage['status'] == 'completed' ? 'bg-success-50 border-success-200' : 'bg-white border-neutral-100' }} transition-all">
                <!-- Status Icon/Number -->
                <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ $stage['status'] == 'completed' ? 'bg-success-500 text-white' : 'bg-neutral-100 text-neutral-400' }}">
                    @if($stage['status'] == 'completed')
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    @else
                    {{ $stage['id'] }}
                    @endif
                </div>

                <!-- Stage Info -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-neutral-900">{{ $stage['name'] }}</p>
                    <p class="text-xs text-neutral-500 truncate">{{ $stage['desc'] }}</p>
                </div>

                <!-- Action Button -->
                @if($stage['status'] == 'pending')
                <button class="shrink-0 px-4 py-1.5 bg-primary-600 text-white text-[10px] font-bold rounded-md hover:bg-primary-700 transition-colors uppercase tracking-wider">
                    Start
                </button>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
