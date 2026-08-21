<aside id="sidebar" 
    class="fixed left-0 top-0 z-200 h-screen bg-[#0b4095] text-white flex flex-col transition-all duration-300 w-64 group-[.collapsed]:w-16 overflow-hidden">
    
    @php
    $unreadMessagesCount = 0;
    if(auth()->check()) {
        $userConns = auth()->user()->connections();
        if($userConns) {
            $unreadMessagesCount = \App\Models\Message::where('sender_id', '!=', auth()->id())
                ->whereNull('read_at')
                ->whereIn('connection_id', $userConns->pluck('id'))
                ->count();
        }
    }
@endphp

    <!-- Sidebar Toggle Row -->
    <div class="flex items-center h-16 border-b border-white/10 shrink-0 overflow-hidden">
        <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
            <button id="sidebarToggle" class="flex items-center justify-center w-8 h-8 bg-transparent border-none rounded-md text-white cursor-pointer hover:bg-white/10 transition-colors shrink-0">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 12H21M3 6H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <span class="ml-3 text-xl font-semibold tracking-tight transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">
                {{ (auth()->user()->role ?? '') === 'admin' ? 'Admin' : ((auth()->user()->role ?? '') === 'manufacturer' ? 'Manufacturer' : 'Shop Owner') }}
            </span>
        </div>
    </div>

    <!-- User Info -->
    <div class="flex items-center py-5 border-b border-white/10 shrink-0 overflow-hidden">
        <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
            <div class="w-8 h-8 shrink-0 bg-blue-500/20 border border-white/20 rounded-full flex items-center justify-center font-bold text-white shadow-sm">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="ml-3 transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">
                <p class="text-sm font-semibold leading-tight">{{ auth()->user()->name ?? 'User' }}</p>
                <p class="text-[11px] opacity-70 leading-tight">{{ (auth()->user()->role ?? '') === 'admin' ? 'Administrator' : ((auth()->user()->role ?? '') === 'manufacturer' ? 'Manufacturer' : 'Shop Owner') }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 py-4 overflow-y-auto no-scrollbar">
        @if(auth()->user()->role === 'manufacturer')
            <!-- Manufacturer Navigation -->
            <a href="{{ route('manufacturer.dashboard') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('manufacturer.dashboard') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Dashboard</span>
                </div>
            </a>
            
            <a href="{{ route('manufacturer.orders.index') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('manufacturer.orders.*') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 4H8C6.89543 4 6 4.89543 6 6V20C6 21.1046 6.89543 22 8 22H16C17.1046 22 18 21.1046 18 20V6C18 4.89543 17.1046 4 16 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 14H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 18H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 4V2C8 1.44772 8.44772 1 9 1H15C15.5523 1 16 1.44772 16 2V4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Incoming Orders</span>
                </div>
            </a>

            <a href="{{ route('manufacturer.catalog.index') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('manufacturer.catalog.*') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20M4 19.5V5A2.5 2.5 0 0 1 6.5 2.5H20V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Catalog/Products</span>
                </div>
            </a>

            <a href="{{ route('manufacturer.production.index') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('manufacturer.production.*') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Production</span>
                </div>
            </a>

            <a href="{{ route('manufacturer.payments.index') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('manufacturer.payments.*') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 10H21M7 15H8M12 15H13M6 19H18C19.1046 19 20 18.1046 20 17V7C20 5.89543 19.1046 5 18 5H6C4.89543 5 4 5.89543 4 7V17C4 18.1046 4.89543 19 6 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Payments</span>
                </div>
            </a>

            <a href="{{ route('manufacturer.connections.index') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('manufacturer.connections.*') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Connections</span>
                </div>
            </a>

            <a href="{{ route('chat.index') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('chat.*') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center relative">
                    <div class="relative inline-block shrink-0">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        @if($unreadMessagesCount > 0)
                        <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full border border-[#11409c]"></span>
                        @endif
                    </div>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Messages</span>
                </div>
            </a>

            <a href="{{ route('manufacturer.reports.index') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('manufacturer.reports.*') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 20V10M12 20V4M6 20V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Reports</span>
                </div>
            </a>
        @elseif(auth()->user()->role === 'admin')
            <!-- Admin Navigation -->
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Dashboard</span>
                </div>
            </a>

            <a href="{{ route('admin.users') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('admin.users*') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Users</span>
                </div>
            </a>

            <a href="{{ route('admin.reports') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('admin.reports') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 20V10M12 20V4M6 20V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Reports</span>
                </div>
            </a>
        @else
            <!-- Shop Owner Navigation (Default) -->
            <a href="{{ route('shop.dashboard') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('shop.dashboard') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Dashboard</span>
                </div>
            </a>
            
            <a href="{{ route('shop.orders.create') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('shop.orders.create') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Create Order</span>
                </div>
            </a>

            <a href="{{ route('shop.orders.index') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('shop.orders.index') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 4H8C6.89543 4 6 4.89543 6 6V20C6 21.1046 6.89543 22 8 22H16C17.1046 22 18 21.1046 18 20V6C18 4.89543 17.1046 4 16 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 14H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 18H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 4V2C8 1.44772 8.44772 1 9 1H15C15.5523 1 16 1.44772 16 2V4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">My Orders</span>
                </div>
            </a>

            <a href="{{ route('shop.connections') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('shop.connections') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Connections</span>
                </div>
            </a>

            <a href="{{ route('chat.index') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('chat.*') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center relative">
                    <div class="relative inline-block shrink-0">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        @if($unreadMessagesCount > 0)
                        <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full border border-[#11409c]"></span>
                        @endif
                    </div>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Messages</span>
                </div>
            </a>

            <a href="{{ route('shop.payments') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('shop.payments') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 10H21M7 15H8M12 15H13M6 19H18C19.1046 19 20 18.1046 20 17V7C20 5.89543 19.1046 5 18 5H6C4.89543 5 4 5.89543 4 7V17C4 18.1046 4.89543 19 6 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Payments</span>
                </div>
            </a>

            <a href="{{ route('shop.reports') }}" 
               class="flex items-center h-12 text-white no-underline transition-all duration-200 border-l-4 {{ request()->routeIs('shop.reports') ? 'bg-white/10 border-blue-400' : 'hover:bg-white/5 border-transparent' }}">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 20V10M12 20V4M6 20V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="ml-3 text-sm transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Reports</span>
                </div>
            </a>
        @endif
    </nav>

    <!-- Footer -->
    <div class="py-4 border-t border-white/10 shrink-0">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center h-12 bg-transparent border-none text-white cursor-pointer text-sm opacity-70 hover:opacity-100 transition-all border-l-4 border-transparent">
                <div class="flex items-center w-full px-4 group-[.collapsed]:px-0 group-[.collapsed]:justify-center">
                    <svg width="16" height="16" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9M16 17L21 12M21 12L16 7M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="ml-3 transition-all duration-300 group-[.collapsed]:opacity-0 group-[.collapsed]:w-0 whitespace-nowrap overflow-hidden">Logout</span>
                </div>
            </button>
        </form>
    </div>

    <script>
        (function() {
            // Early check for localStorage to avoid animation on load
            const sidebarState = localStorage.getItem('sidebar_collapsed');
            if (sidebarState === 'true') {
                // Add a style tag to disable transitions temporarily
                const style = document.createElement('style');
                style.id = 'sidebar-no-transition';
                style.innerHTML = `
                    #layoutWrapper, #sidebar, #mainWrapper, #sidebar *, #mainWrapper * {
                        transition: none !important;
                    }
                `;
                document.head.appendChild(style);

                // Try to apply the class as soon as possible
                const applyState = () => {
                    const layoutWrapper = document.getElementById('layoutWrapper');
                    if (layoutWrapper) {
                        layoutWrapper.classList.add('collapsed');
                        // Remove the style tag after a short delay once class is applied
                        setTimeout(() => {
                            const s = document.getElementById('sidebar-no-transition');
                            if (s) s.remove();
                        }, 100);
                        return true;
                    }
                    return false;
                };

                if (!applyState()) {
                    const observer = new MutationObserver((mutations, obs) => {
                        if (applyState()) obs.disconnect();
                    });
                    observer.observe(document.documentElement, { childList: true, subtree: true });
                }
            }
        })();

        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const layoutWrapper = document.getElementById('layoutWrapper');
            const 
            
            if (sidebarToggle && layoutWrapper) {
                sidebarToggle.addEventListener('click', function() {
                    const isCollapsed = layoutWrapper.classList.toggle('collapsed');
                    localStorage.setItem('sidebar_collapsed', isCollapsed);
                });
            }
        });
    </script>
</aside>
