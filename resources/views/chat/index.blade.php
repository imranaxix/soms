@extends('layouts.app')

@section('title', 'Messages - SOMS')
@section('hide_page_header', true)

@section('content')
<div class="max-w-6xl mx-auto px-4 pb-0 h-[calc(100vh-112px)] flex flex-col">

    <div class="flex-1 flex bg-white rounded-3xl border border-neutral-100 shadow-sm overflow-hidden">
        
        {{-- ── Left: Contact List ── --}}
        <div class="w-80 border-r-2 border-neutral-200 flex flex-col shrink-0 bg-neutral-50/50">
            <div class="px-5 py-4 border-b border-neutral-100 bg-white flex flex-col gap-3 shrink-0">
                <h3 class="text-sm font-black text-neutral-900">Connections</h3>
                <div class="relative">
                    <input type="text" id="conn-search" placeholder="Search..." class="w-full pl-9 pr-3 py-2 bg-neutral-50 border border-neutral-200 rounded-xl text-[13px] font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder:text-neutral-400">
                    <svg class="absolute left-3 top-2.5 text-neutral-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto">
                @forelse($connections as $conn)
                @php
                    $p = $conn->shop_owner_id === auth()->id() ? $conn->manufacturer : $conn->shopOwner;
                    $pName = $p->business_name ?? $p->name;
                    $pInit = strtoupper(substr($pName, 0, 1));
                    $latest = $conn->latestMessage->first();
                    $unread = $unreadCounts[$conn->id] ?? 0;
                @endphp
                <a href="{{ route('chat.show', $conn->id) }}" data-name="{{ $pName }}" class="conn-item flex items-center gap-4 px-5 py-4 hover:bg-white transition-colors border-b border-neutral-100/50 group">
                    <div class="relative shrink-0">
                        @if($p->profile_image)
                        <img src="{{ asset('storage/' . $p->profile_image) }}" alt="{{ $pName }}" class="w-12 h-12 rounded-2xl object-cover shadow-sm">
                        @else
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white font-black flex items-center justify-center text-lg shadow-sm">
                            {{ $pInit }}
                        </div>
                        @endif
                        @if($unread > 0)
                        <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white text-[10px] font-black rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                            {{ $unread > 9 ? '9+' : $unread }}
                        </span>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline mb-0.5">
                            <h4 class="text-sm font-bold text-neutral-900 truncate group-hover:text-indigo-600 transition-colors">{{ $pName }}</h4>
                            @if($latest)
                            <span class="text-[10px] font-medium text-neutral-400 shrink-0 ml-2">
                                {{ $latest->created_at->isToday() ? $latest->created_at->format('g:i A') : $latest->created_at->format('M j') }}
                            </span>
                            @endif
                        </div>
                        <p class="text-xs {{ $unread > 0 ? 'text-neutral-800 font-bold' : 'text-neutral-500 font-medium' }} truncate">
                            @if($latest)
                                @if($latest->sender_id === auth()->id())
                                    <span class="text-neutral-400 font-normal">You:</span> 
                                @endif
                                {{ $latest->body }}
                            @else
                                <span class="italic text-neutral-400 font-normal">No messages yet</span>
                            @endif
                        </p>
                    </div>
                </a>
                @empty
                <div class="p-8 text-center">
                    <div class="w-12 h-12 bg-neutral-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-neutral-500">No conversations</p>
                    <p class="text-[10px] text-neutral-400 mt-1">Accept a connection to chat.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── Right: Empty State ── --}}
        <div class="flex-1 flex flex-col items-center justify-center bg-white bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px]">
            <div class="w-20 h-20 bg-white rounded-3xl shadow-sm border border-neutral-100 flex items-center justify-center mb-6">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <h2 class="text-lg font-black text-neutral-800">Your Messages</h2>
            <p class="text-sm font-medium text-neutral-500 mt-2">Select a conversation from the sidebar to start chatting.</p>
        </div>
        
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('conn-search');
        if(searchInput) {
            searchInput.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('.conn-item').forEach(item => {
                    const name = item.dataset.name.toLowerCase();
                    item.style.display = name.includes(term) ? 'flex' : 'none';
                });
            });
        }
    });
</script>
@endsection
