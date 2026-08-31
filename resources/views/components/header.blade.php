<header class="h-16 bg-white border-b border-neutral-200 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-[190] shadow-sm">
    <!-- Brand/Logo (matching Header.js) -->
    <div class="flex items-center gap-3">
        <button id="mobileMenuBtn" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-lg text-neutral-500 hover:bg-neutral-100 transition-all" aria-label="Menu" title="Menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 12H21M3 6H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center text-white shadow-md shadow-primary-600/20">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <span class="text-xl font-bold tracking-tight text-neutral-900 leading-none">SOMS</span>
    </div>

    <!-- Right Side Actions (matching Header.js) -->
    <div class="flex items-center gap-2">
       

        <!-- Notifications -->
        <div class="relative" style="z-index: 1;">
            <button id="notificationBtn" onclick="toggleNotifications()" class="w-10 h-10 flex items-center justify-center rounded-lg text-neutral-500 hover:bg-neutral-100 transition-all relative" title="Notifications">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 8C18 6.4087 17.3679 4.88258 16.2426 3.75736C15.1174 2.63214 13.5913 2 12 2C10.4087 2 8.88258 2.63214 7.75736 3.75736C6.63214 4.88258 6 6.4087 6 8C6 15 3 17 3 17H21C21 17 18 15 18 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M13.73 21C13.5542 21.3031 13.3019 21.5547 12.9982 21.7295C12.6946 21.9044 12.3504 21.9965 12 21.9965C11.6496 21.9965 11.3054 21.9044 11.0018 21.7295C10.6982 21.5547 10.4458 21.3031 10.27 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                @if(auth()->user()->unreadNotifications->count() > 0)
                <span class="absolute top-2.5 right-2.5 w-2.5 h-2.5 bg-error-500 rounded-full border-2 border-white"></span>
                @endif
            </button>
            <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white rounded-xl shadow-xl border border-neutral-200 z-50">
                <div class="p-4 border-b border-neutral-100 flex justify-between items-center">
                    <h3 class="font-bold text-neutral-900">Notifications</h3>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                    <div class="flex items-center gap-2">
                        <form action="{{ route('notifications.markAllRead') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-[10px] font-bold text-primary-600 hover:text-primary-800 uppercase tracking-wider">Mark all as read</button>
                        </form>
                        <span class="text-[10px] font-semibold text-primary-600 bg-primary-50 px-2 py-0.5 rounded-full">{{ auth()->user()->unreadNotifications->count() }} New</span>
                    </div>
                    @endif
                </div>
                <div class="max-h-[300px] overflow-y-auto w-full">
                    @forelse(auth()->user()->notifications->take(5) as $notification)
                    <a href="{{ route('notifications.read', $notification->id) }}" class="block p-4 border-b border-neutral-50 hover:bg-neutral-50 transition-colors text-left flex flex-col items-start {{ is_null($notification->read_at) ? 'bg-primary-50/50' : '' }}">
                        <p class="text-sm font-semibold text-neutral-900 w-full text-left">{{ $notification->data['message'] ?? 'Notification' }}</p>
                        <p class="text-xs text-neutral-500 mt-1 pb-1 w-full text-left">{{ $notification->data['details'] ?? '' }}</p>
                        <p class="text-[10px] text-neutral-400 mt-1 w-full text-left">{{ $notification->created_at->diffForHumans() }}</p>
                    </a>
                    @empty
                    <div class="p-8 text-center text-neutral-500 text-sm">
                        No notifications yet.
                    </div>
                    @endforelse
                </div>
                <div class="border-t border-neutral-100">
                    <a href="{{ route('notifications.index') }}" class="block w-full text-center px-4 py-3 text-xs font-bold text-primary-600 hover:bg-primary-50 transition-colors">
                        See All Notifications →
                    </a>
                </div>
            </div>
        </div>

        <!-- Profile -->
        <a href="{{ auth()->user()->role === 'manufacturer' ? route('manufacturer.profile') : (auth()->user()->role === 'admin' ? route('admin.dashboard') : route('shop.profile')) }}" class="w-10 h-10 flex items-center justify-center rounded-full text-neutral-500 hover:bg-neutral-100 transition-all relative" title="profile">
            @if(auth()->user()->profile_image)
                <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-neutral-200">
            @else
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path fillRule="evenodd" d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" clipRule="evenodd" />
                </svg>
            @endif
        </a>
    </div>
</header>
<script>
    function toggleNotifications() {
        const dropdown = document.getElementById('notificationDropdown');
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    }
    document.addEventListener('click', function(event) {
        const btn = document.getElementById('notificationBtn');
        const dropdown = document.getElementById('notificationDropdown');
        if (btn && dropdown && !btn.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>
