@extends('layouts.app')

@section('title', 'All Notifications - SOMS')
@section('page_title', 'Notifications')
@section('page_subtitle', 'All your notifications in one place.')
@section('header_actions')
    @if(auth()->user()->unreadNotifications->count() > 0)
    <form action="{{ route('notifications.markAllRead') }}" method="POST">
        @csrf
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg font-bold text-sm hover:bg-primary-700 transition-colors shadow-sm shadow-primary-500/20">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17L4 12"/></svg>
            Mark All as Read
        </button>
    </form>
    @endif
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-3">

    @if(session('success'))
    <div class="flex items-center gap-3 px-5 py-3.5 bg-success-50 border border-success-200 rounded-2xl text-success-700 text-sm font-bold">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17L4 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-3xl border border-neutral-100 shadow-sm overflow-hidden">

        {{-- Header row --}}
        <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary-50 flex items-center justify-center">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8C18 6.4087 17.3679 4.88258 16.2426 3.75736C15.1174 2.63214 13.5913 2 12 2C10.4087 2 8.88258 2.63214 7.75736 3.75736C6.63214 4.88258 6 6.4087 6 8C6 15 3 17 3 17H21C21 17 18 15 18 8Z"/>
                        <path d="M13.73 21C13.5542 21.3031 13.3019 21.5547 12.9982 21.7295C12.6946 21.9044 12.3504 21.9965 12 21.9965C11.6496 21.9965 11.3054 21.9044 11.0018 21.7295C10.6982 21.5547 10.4458 21.3031 10.27 21"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-black text-neutral-900">All Notifications</h2>
                    <p class="text-[11px] text-neutral-500 font-medium">{{ $notifications->total() }} total · {{ auth()->user()->unreadNotifications->count() }} unread</p>
                </div>
            </div>
        </div>

        {{-- Notification list --}}
        @forelse($notifications as $notification)
        @php
            $isUnread = is_null($notification->read_at);
            $title   = $notification->data['message'] ?? $notification->data['title'] ?? 'Notification';
            $details = $notification->data['details'] ?? $notification->data['body'] ?? '';
            $url     = $notification->data['url'] ?? null;
        @endphp
        <a href="{{ route('notifications.read', $notification->id) }}"
           class="flex items-start gap-4 px-6 py-4 border-b border-neutral-50 hover:bg-neutral-50/80 transition-colors group {{ $isUnread ? 'bg-primary-50/30' : 'bg-white' }}">

            {{-- Unread dot --}}
            <div class="mt-1.5 shrink-0">
                @if($isUnread)
                    <span class="w-2.5 h-2.5 bg-primary-500 rounded-full block ring-4 ring-primary-100"></span>
                @else
                    <span class="w-2.5 h-2.5 bg-neutral-200 rounded-full block"></span>
                @endif
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <p class="text-[13px] font-{{ $isUnread ? 'bold' : 'semibold' }} text-neutral-900 leading-snug">{{ $title }}</p>
                @if($details)
                <p class="text-xs text-neutral-500 mt-0.5 font-medium leading-relaxed">{{ $details }}</p>
                @endif
                <p class="text-[11px] text-neutral-400 font-medium mt-1.5">{{ $notification->created_at->diffForHumans() }}</p>
            </div>

            {{-- Arrow --}}
            <div class="shrink-0 mt-1 text-neutral-300 group-hover:text-primary-500 transition-colors">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12H19M19 12L12 5M19 12L12 19"/></svg>
            </div>
        </a>
        @empty
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 bg-neutral-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8C18 6.4087 17.3679 4.88258 16.2426 3.75736C15.1174 2.63214 13.5913 2 12 2C10.4087 2 8.88258 2.63214 7.75736 3.75736C6.63214 4.88258 6 6.4087 6 8C6 15 3 17 3 17H21C21 17 18 15 18 8Z"/>
                    <path d="M13.73 21C13.5542 21.3031 13.3019 21.5547 12.9982 21.7295C12.6946 21.9044 12.3504 21.9965 12 21.9965C11.6496 21.9965 11.3054 21.9044 11.0018 21.7295C10.6982 21.5547 10.4458 21.3031 10.27 21"/>
                </svg>
            </div>
            <p class="text-[15px] font-black text-neutral-600">All caught up!</p>
            <p class="text-xs text-neutral-400 font-medium mt-1.5">No notifications to show yet.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div class="flex items-center justify-between px-1 py-2">
        <p class="text-xs text-neutral-500 font-medium">
            Showing {{ $notifications->firstItem() }}–{{ $notifications->lastItem() }} of {{ $notifications->total() }}
        </p>
        <div class="flex items-center gap-1.5">
            @if($notifications->onFirstPage())
                <span class="w-9 h-9 flex items-center justify-center rounded-xl bg-neutral-100 text-neutral-400 cursor-not-allowed">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18L9 12L15 6"/></svg>
                </span>
            @else
                <a href="{{ $notifications->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-neutral-200 text-neutral-600 hover:bg-primary-50 hover:border-primary-300 hover:text-primary-600 transition-colors shadow-sm">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18L9 12L15 6"/></svg>
                </a>
            @endif

            @foreach($notifications->getUrlRange(max(1, $notifications->currentPage()-2), min($notifications->lastPage(), $notifications->currentPage()+2)) as $page => $url)
                @if($page == $notifications->currentPage())
                    <span class="w-9 h-9 flex items-center justify-center rounded-xl bg-primary-600 text-white font-bold text-sm shadow-md shadow-primary-500/20">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-neutral-200 text-neutral-600 hover:bg-primary-50 hover:border-primary-300 hover:text-primary-600 font-semibold text-sm transition-colors shadow-sm">{{ $page }}</a>
                @endif
            @endforeach

            @if($notifications->hasMorePages())
                <a href="{{ $notifications->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-neutral-200 text-neutral-600 hover:bg-primary-50 hover:border-primary-300 hover:text-primary-600 transition-colors shadow-sm">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18L15 12L9 6"/></svg>
                </a>
            @else
                <span class="w-9 h-9 flex items-center justify-center rounded-xl bg-neutral-100 text-neutral-400 cursor-not-allowed">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18L15 12L9 6"/></svg>
                </span>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection
