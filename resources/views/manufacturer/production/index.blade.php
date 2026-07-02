@extends('layouts.app')

@section('title', 'Production Management - Manufacturer')
@section('page_title', 'Production Management')
@section('page_subtitle', 'Track and update production stages for active orders')

@section('content')

{{-- Flash Messages --}}
@if(session('success'))
    <div class="flex items-center gap-3 px-5 py-4 mb-6 bg-success-50 border border-success-200 text-success-700 rounded-xl text-sm font-medium">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 13.01 9 10.01"/></svg>
        {{ session('success') }}
    </div>
@endif

@if($orders->isEmpty())
    <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm p-16 text-center mt-8">
        <div class="w-16 h-16 bg-neutral-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
        </div>
        <p class="text-neutral-500 font-medium">No orders currently in production.</p>
        <p class="text-neutral-400 text-sm mt-1">Orders you accept will appear here with their production stages.</p>
    </div>
@else
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-2">
    @foreach($orders as $order)
    @php
        $totalStages     = $order->stages->count();
        $completedStages = $order->stages->where('status', 'completed')->count();
        $currentStage    = $order->stages->where('status', 'pending')->first();
    @endphp
    <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm p-8 space-y-6">

        {{-- Order Header --}}
        <div class="flex justify-between items-start">
            <div class="space-y-1">
                <a href="{{ route('manufacturer.orders.show', $order->id) }}" class="text-xl font-bold text-neutral-900 hover:text-primary-600 transition-colors">
                    Order {{ $order->order_number }}
                </a>
                <p class="text-sm text-neutral-500 font-medium">
                    {{ $order->product->name }} — {{ number_format($order->quantity) }} {{ $order->unit }}
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-50 text-blue-700 border border-blue-100">
                {{ $order->progress_percent }}%
            </span>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-3 gap-4 bg-neutral-50 p-4 rounded-xl border border-neutral-100">
            <div>
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-wider mb-1">Shop Owner</p>
                <p class="text-sm font-bold text-neutral-800 truncate">{{ $order->shopOwner->business_name ?? $order->shopOwner->name }}</p>
            </div>
            <div>
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-wider mb-1">Due Date</p>
                <p class="text-sm font-bold text-neutral-800">{{ $order->due_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-[10px] text-neutral-400 uppercase font-bold tracking-wider mb-1">Current Stage</p>
                <p class="text-sm font-bold text-neutral-800 truncate">{{ $currentStage ? $currentStage->name : 'All Done ✓' }}</p>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div>
            <div class="flex justify-between text-xs font-bold text-neutral-400 mb-1.5">
                <span>{{ $completedStages }}/{{ $totalStages }} stages complete</span>
                <span>{{ $order->progress_percent }}%</span>
            </div>
            <div class="w-full h-2.5 bg-neutral-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700
                    {{ $order->progress_percent === 100 ? 'bg-success-500' : 'bg-primary-500' }}"
                    style="width: {{ $order->progress_percent }}%">
                </div>
            </div>
        </div>

        {{-- Production Stages List --}}
        @if($totalStages > 0)
        <div class="space-y-3">
            @foreach($order->stages as $index => $stage)
            <div class="flex items-center gap-4 p-4 rounded-xl border transition-all
                {{ $stage->status === 'completed' ? 'bg-success-50 border-success-200' : 'bg-white border-neutral-100' }}">

                {{-- Status Indicator --}}
                <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                    {{ $stage->status === 'completed' ? 'bg-success-500 text-white' : 'bg-neutral-100 text-neutral-400' }}">
                    @if($stage->status === 'completed')
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @else
                        {{ $index + 1 }}
                    @endif
                </div>

                {{-- Stage Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-neutral-900">{{ $stage->name }}</p>
                    @if($stage->description)
                        <p class="text-xs text-neutral-400 truncate">{{ $stage->description }}</p>
                    @endif
                    @if($stage->completed_at)
                        <p class="text-[10px] text-success-600 font-medium mt-0.5">Done {{ $stage->completed_at->format('M d, H:i') }}</p>
                    @endif
                </div>

                {{-- Toggle Button --}}
                <form action="{{ route('manufacturer.orders.stages.toggle', [$order->id, $stage->id]) }}" method="POST">
                    @csrf
                    @if($stage->status === 'completed')
                        <button type="submit"
                            class="shrink-0 px-3 py-1.5 bg-white text-success-600 border border-success-200 text-[10px] font-bold rounded-lg hover:bg-success-50 transition-colors uppercase tracking-wider">
                            Undo
                        </button>
                    @else
                        <button type="submit"
                            class="shrink-0 px-3 py-1.5 bg-primary-600 text-white text-[10px] font-bold rounded-lg hover:bg-primary-700 transition-colors uppercase tracking-wider">
                            Complete
                        </button>
                    @endif
                </form>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-6 bg-neutral-50 rounded-xl border border-dashed border-neutral-200">
            <p class="text-sm text-neutral-400 font-medium">No production stages defined for this product.</p>
            <p class="text-xs text-neutral-400 mt-1">Edit the product to add stages.</p>
        </div>
        @endif

    </div>
    @endforeach
</div>
@endif

@endsection
