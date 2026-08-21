@extends('layouts.app')

@section('title', 'Chat with ' . ($partner->business_name ?? $partner->name) . ' - SOMS')
@section('hide_page_header', true)

@section('content')
<div class="max-w-6xl mx-auto px-4 pb-0 h-[calc(100vh-112px)] flex flex-col">

    <div class="flex-1 flex bg-white rounded-3xl border border-neutral-100 shadow-sm overflow-hidden">
        
        {{-- ── Left: Contact List ── --}}
        <div class="w-80 border-r-2 border-neutral-200 flex flex-col shrink-0 bg-neutral-50/50 hidden md:flex">
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
                    $isActive = $conn->id === $connection->id;
                @endphp
                <a href="{{ route('chat.show', $conn->id) }}" data-name="{{ $pName }}" class="conn-item flex items-center gap-4 px-5 py-4 transition-colors border-b border-neutral-100/50 group {{ $isActive ? 'bg-white shadow-[inset_4px_0_0_#4f46e5]' : 'hover:bg-white' }}">
                    <div class="relative shrink-0">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $isActive ? 'from-indigo-600 to-blue-700 shadow-md shadow-indigo-100' : 'from-neutral-400 to-neutral-500' }} text-white font-black flex items-center justify-center text-lg transition-all">
                            {{ $pInit }}
                        </div>
                        @if($unread > 0 && !$isActive)
                        <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white text-[10px] font-black rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                            {{ $unread > 9 ? '9+' : $unread }}
                        </span>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline mb-0.5">
                            <h4 class="text-sm font-bold truncate transition-colors {{ $isActive ? 'text-indigo-700' : 'text-neutral-900 group-hover:text-indigo-600' }}">{{ $pName }}</h4>
                            @if($latest)
                            <span class="text-[10px] font-medium shrink-0 ml-2 {{ $isActive ? 'text-indigo-400' : 'text-neutral-400' }}">
                                {{ $latest->created_at->isToday() ? $latest->created_at->format('g:i A') : $latest->created_at->format('M j') }}
                            </span>
                            @endif
                        </div>
                        <p class="text-xs truncate {{ $isActive ? 'text-indigo-900/60 font-medium' : ($unread > 0 ? 'text-neutral-800 font-bold' : 'text-neutral-500 font-medium') }}">
                            @if($latest)
                                @if($latest->sender_id === auth()->id())
                                    <span class="opacity-70 font-normal">You:</span> 
                                @endif
                                {{ $latest->body }}
                            @else
                                <span class="italic opacity-70 font-normal">No messages yet</span>
                            @endif
                        </p>
                    </div>
                </a>
                @empty
                <div class="p-8 text-center">
                    <p class="text-xs font-bold text-neutral-500">No conversations</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── Right: Conversation Thread ── --}}
        <div class="flex-1 flex flex-col bg-white overflow-hidden relative">

            {{-- Mobile back button & Partner header --}}
            <div class="flex items-center gap-4 px-6 py-4 border-b border-neutral-100 bg-white shrink-0 z-10 shadow-sm shadow-neutral-100/50">
                <a href="{{ route('chat.index') }}" class="md:hidden w-10 h-10 flex items-center justify-center rounded-xl bg-neutral-100 text-neutral-600 hover:bg-neutral-200 transition-colors">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M5 12L12 19M5 12L12 5"/></svg>
                </a>
                
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white font-black flex items-center justify-center text-lg shadow-sm">
                    {{ strtoupper(substr($partner->business_name ?? $partner->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm font-black text-neutral-900 truncate">{{ $partner->business_name ?? $partner->name }}</h2>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400 inline-block shadow-[0_0_8px_rgba(74,222,128,0.5)]"></span>
                        <p class="text-xs text-neutral-400 font-medium truncate">Connected Partner</p>
                    </div>
                </div>
                
                <a href="{{ route('user.show', $partner->id) }}" class="w-9 h-9 flex items-center justify-center rounded-xl text-neutral-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="View Profile">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </a>
            </div>

            {{-- Message thread --}}
            <div id="message-thread" class="flex-1 overflow-y-auto px-6 py-6 space-y-5 scroll-smooth bg-[#fafafa]">
                @php
                    $lastReadMineId = $messages->where('sender_id', auth()->id())->whereNotNull('read_at')->last()?->id;
                @endphp
                @forelse($messages as $msg)
                @php $isMine = $msg->sender_id === auth()->id(); @endphp
                <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }} group" data-msg-id="{{ $msg->id }}">
                    @unless($isMine)
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 text-white text-xs font-black flex items-center justify-center shrink-0 mr-3 mt-auto mb-5 shadow-sm">
                        {{ strtoupper(substr($partner->business_name ?? $partner->name, 0, 1)) }}
                    </div>
                    @endunless

                    <div class="max-w-[70%]">
                        <div class="px-5 py-3 rounded-2xl text-[13px] leading-relaxed
                            {{ $isMine
                                ? 'bg-gradient-to-br from-indigo-600 to-blue-600 text-white rounded-br-sm font-medium shadow-md shadow-indigo-200/50'
                                : 'bg-white text-neutral-800 rounded-bl-sm font-medium shadow-sm border border-neutral-100' }}">
                            {{ $msg->body }}
                        </div>
                        <p class="text-[10px] text-neutral-400 mt-1.5 font-medium {{ $isMine ? 'text-right pr-1' : 'text-left pl-1' }}">
                            {{ $msg->created_at->format('g:i A') }}
                            @if($isMine && $msg->id === $lastReadMineId)
                            · <span class="text-indigo-400">Seen</span>
                            @endif
                        </p>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center h-full text-center py-12" id="empty-state">
                    <div class="w-16 h-16 rounded-3xl bg-indigo-50 flex items-center justify-center mb-5">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <p class="text-[15px] font-black text-neutral-800">Say Hello!</p>
                    <p class="text-xs text-neutral-500 font-medium mt-1.5">Start the conversation with {{ $partner->business_name ?? $partner->name }}</p>
                </div>
                @endforelse
            </div>

            {{-- Input bar --}}
            <div class="px-6 py-4 bg-white border-t border-neutral-100 shrink-0 z-10 shadow-[0_-4px_20px_rgba(0,0,0,0.02)]">
                <div id="send-error" class="hidden mb-2 text-xs text-red-500 font-bold px-2"></div>
                <form id="chat-form" class="flex items-end gap-3 relative">
                    @csrf
                    <div class="flex-1 relative">
                        <textarea
                            id="chat-input"
                            rows="1"
                            placeholder="Type a message..."
                            maxlength="2000"
                            class="w-full px-5 py-3.5 bg-neutral-50 border border-neutral-200 rounded-2xl text-[13px] font-medium text-neutral-900 focus:ring-4 focus:ring-indigo-500/15 focus:border-indigo-500 outline-none transition-all placeholder:text-neutral-400 resize-none max-h-32"
                            style="min-height: 48px;"
                        ></textarea>
                    </div>
                    <button
                        type="submit"
                        id="send-btn"
                        class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white flex items-center justify-center hover:opacity-90 active:scale-95 transition-all shadow-md shadow-indigo-200 shrink-0 disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="ml-0.5"><path d="M22 2L11 13M22 2L15 22 11 13 2 9l20-7z"/></svg>
                    </button>
                </form>
            </div>
            
        </div>
    </div>
</div>

<style>
@keyframes msg-in {
    from { opacity: 0; transform: translateY(10px) scale(0.98); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.msg-anim { animation: msg-in 0.25s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
textarea::-webkit-scrollbar { width: 6px; }
textarea::-webkit-scrollbar-track { background: transparent; }
textarea::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
</style>

<script>
(function () {
    const thread    = document.getElementById('message-thread');
    const form      = document.getElementById('chat-form');
    const input     = document.getElementById('chat-input');
    const sendBtn   = document.getElementById('send-btn');
    const errorBox  = document.getElementById('send-error');
    
    const connId    = {{ $connection->id }};
    const pollUrl   = "{{ route('chat.poll', $connection->id) }}";
    const sendUrl   = "{{ route('chat.send', $connection->id) }}";
    const csrf      = "{{ csrf_token() }}";
    const partnerInitial = "{{ strtoupper(substr($partner->business_name ?? $partner->name, 0, 1)) }}";

    let lastId = {{ $messages->last()?->id ?? 0 }};

    function scrollToBottom(smooth = false) {
        thread.scrollTo({ top: thread.scrollHeight, behavior: smooth ? 'smooth' : 'instant' });
    }

    // Auto-resize textarea
    input.addEventListener('input', function() {
        this.style.height = '48px';
        const height = Math.min(this.scrollHeight, 128);
        this.style.height = height + 'px';
    });

    // Connection search filter
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

    function renderBubble(msg) {
        const wrap = document.createElement('div');
        wrap.className = `flex ${msg.is_mine ? 'justify-end' : 'justify-start'} group msg-anim`;
        wrap.dataset.msgId = msg.id;

        if (msg.is_mine) {
            wrap.innerHTML = `
                <div class="max-w-[70%]">
                    <div class="px-5 py-3 rounded-2xl rounded-br-sm text-[13px] leading-relaxed font-medium bg-gradient-to-br from-indigo-600 to-blue-600 text-white shadow-md shadow-indigo-200/50">${msg.body}</div>
                    <p class="text-[10px] text-neutral-400 mt-1.5 font-medium text-right pr-1">${msg.time}</p>
                </div>`;
        } else {
            wrap.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 text-white text-xs font-black flex items-center justify-center shrink-0 mr-3 mt-auto mb-5 shadow-sm">${partnerInitial}</div>
                <div class="max-w-[70%]">
                    <div class="px-5 py-3 rounded-2xl rounded-bl-sm text-[13px] leading-relaxed font-medium bg-white text-neutral-800 shadow-sm border border-neutral-100">${msg.body}</div>
                    <p class="text-[10px] text-neutral-400 mt-1.5 font-medium text-left pl-1">${msg.time}</p>
                </div>`;
        }
        return wrap;
    }

    async function poll() {
        try {
            const res = await fetch(`${pollUrl}?after_id=${lastId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.messages && data.messages.length > 0) {
                const empty = document.getElementById('empty-state');
                if (empty) empty.remove();

                data.messages.forEach(msg => {
                    if (document.querySelector(`[data-msg-id="${msg.id}"]`)) return;
                    thread.appendChild(renderBubble(msg));
                    lastId = Math.max(lastId, msg.id);
                });
                scrollToBottom(true);
            }
        } catch (e) {}
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const body = input.value.trim();
        if (!body) return;

        errorBox.classList.add('hidden');
        errorBox.textContent = '';

        const tempId = 'opt-' + Date.now();
        const optimistic = renderBubble({
            id: tempId,
            body: body,
            is_mine: true,
            time: new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })
        });
        
        const empty = document.getElementById('empty-state');
        if (empty) empty.remove();
        
        thread.appendChild(optimistic);
        scrollToBottom(true);
        
        input.value = '';
        input.style.height = '48px';
        sendBtn.disabled = true;

        try {
            const res = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ body })
            });

            if (!res.ok) throw new Error('Send failed');

            await poll();
            const opt = document.querySelector(`[data-msg-id="${tempId}"]`);
            if (opt) opt.remove();

        } catch (err) {
            const opt = document.querySelector(`[data-msg-id="${tempId}"]`);
            if (opt) opt.remove();
            input.value = body;
            errorBox.textContent = 'Failed to send. Please try again.';
            errorBox.classList.remove('hidden');
        } finally {
            sendBtn.disabled = false;
            input.focus();
        }
    });

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendBtn.click();
        }
    });

    scrollToBottom();
    setInterval(poll, 4000);
})();
</script>
@endsection
