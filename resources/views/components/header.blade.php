<header class="h-16 bg-white border-b border-neutral-200 px-6 flex items-center justify-between sticky top-0 z-190 shadow-sm">
    <!-- Brand/Logo (matching Header.js) -->
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center text-white shadow-md shadow-primary-600/20">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 7L12 3L4 7M20 7L12 11M20 7V17L12 21M12 11L4 7M12 11V21M4 7V17L12 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
        <span class="text-xl font-bold tracking-tight text-neutral-900 leading-none">SOMS</span>
    </div>

    <!-- Right Side Actions (matching Header.js) -->
    <div class="flex items-center gap-2">
        <!-- Theme Toggle -->
        <button class="w-10 h-10 flex items-center justify-center rounded-lg text-neutral-500 hover:bg-neutral-100 transition-all" title="Toggle Theme">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>

        <!-- Notifications -->
        <div class="relative">
            <button id="notificationBtn" onclick="toggleNotifications()" class="w-10 h-10 flex items-center justify-center rounded-lg text-neutral-500 hover:bg-neutral-100 transition-all relative" title="Notifications">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 8C18 6.4087 17.3679 4.88258 16.2426 3.75736C15.1174 2.63214 13.5913 2 12 2C10.4087 2 8.88258 2.63214 7.75736 3.75736C6.63214 4.88258 6 6.4087 6 8C6 15 3 17 3 17H21C21 17 18 15 18 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M13.73 21C13.5542 21.3031 13.3019 21.5547 12.9982 21.7295C12.6946 21.9044 12.3504 21.9965 12 21.9965C11.6496 21.9965 11.3054 21.9044 11.0018 21.7295C10.6982 21.5547 10.4458 21.3031 10.27 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="absolute top-2.5 right-2.5 w-2.5 h-2.5 bg-error-500 rounded-full border-2 border-white"></span>
            </button>
            <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-neutral-200 z-50">
                <div class="p-4 border-b border-neutral-100 flex justify-between items-center">
                    <h3 class="font-bold text-neutral-900">Notifications</h3>
                    <span class="text-xs font-semibold text-primary-600 bg-primary-50 px-2 py-1 rounded-full">2 New</span>
                </div>
                <div class="max-h-[300px] overflow-y-auto w-full">
                    <div class="p-4 border-b border-neutral-50 hover:bg-neutral-50 cursor-pointer transition-colors text-left flex flex-col items-start">
                        <p class="text-sm font-semibold text-neutral-900 w-full text-left">Order #731221 Updated</p>
                        <p class="text-xs text-neutral-500 mt-1 w-full text-left">Status changed to In Progress</p>
                        <p class="text-[10px] py-1 text-neutral-400 mt-1 w-full text-left">2 mins ago</p>
                    </div>
                    <div class="p-4 border-b border-neutral-50 hover:bg-neutral-50 cursor-pointer transition-colors text-left flex flex-col items-start">
                        <p class="text-sm font-semibold text-neutral-900 w-full text-left">New Connection Request</p>
                        <p class="text-xs text-neutral-500 mt-1 pb-1 w-full text-left">ABC Textiles wants to connect.</p>
                        <p class="text-[10px] text-neutral-400 mt-1 w-full text-left">1 hour ago</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile -->
        <a href="{{ route('profile') }}" class="w-8 h-8 flex flex-col items-center justify-center rounded-lg text-neutral-500 hover:bg-neutral-100 transition-all relative" title="profile">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                <path fillRule="evenodd" d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" clipRule="evenodd" />
            </svg>
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
