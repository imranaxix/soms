<aside id="sidebar" 
    class="fixed left-0 top-0 z-200 h-screen bg-[#0b4095] text-white flex flex-col transition-all duration-300 w-60 overflow-hidden group-[.collapsed]:w-13">
    
    <!-- Sidebar Toggle Row -->
    <div class="flex items-center gap-2.5 h-16 px-3.5 border-b border-white/10 shrink-0">
        <button id="sidebarToggle" class="flex items-center justify-center w-8 h-8 bg-transparent border-none rounded-md text-white cursor-pointer hover:bg-white/10 transition-colors">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 12H21M3 6H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
        <span class="text-xl font-bold tracking-widest whitespace-nowrap opacity-100 transition-opacity group-[.collapsed]:opacity-0">SOMS</span>
    </div>

    <!-- User Info -->
    <div class="flex items-center gap-3 px-3.5 py-5 border-b border-white/10 shrink-0 overflow-hidden">
        <div class="w-9 h-9 shrink-0 bg-linear-to-br from-[#6366f1] to-[#818cf8] rounded-full flex items-center justify-center font-bold">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
        <div class="user-info transition-opacity duration-300 group-[.collapsed]:opacity-0 whitespace-nowrap">
            <p class="text-[13px] font-semibold leading-tight">{{ auth()->user()->name ?? 'User' }}</p>
            <p class="text-[11px] opacity-70 leading-tight">{{ (auth()->user()->role ?? '') === 'manufacturer' ? 'Manufacturer' : 'Shop Owner' }}</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 py-5 overflow-y-auto no-scrollbar">
        @if(request()->is('manufacturer*'))
            <!-- Manufacturer Navigation -->
            <a href="{{ route('manufacturer.dashboard') }}" 
               class="flex items-center gap-3 px-3.5 py-3 text-sm text-white no-underline transition-colors {{ request()->routeIs('manufacturer.dashboard') ? 'bg-white/10' : 'hover:bg-white/10' }}">
                <svg width="18" height="18" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="transition-opacity duration-300 group-[.collapsed]:opacity-0 whitespace-nowrap">Dashboard</span>
            </a>
            
            <a href="{{ route('manufacturer.orders.index') }}" 
               class="flex items-center gap-3 px-3.5 py-3 text-sm text-white no-underline transition-colors {{ request()->routeIs('manufacturer.orders.*') ? 'bg-white/10' : 'hover:bg-white/10' }}">
                <svg width="18" height="18" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 5H7C6.46957 5 5.96086 5.21071 5.58579 5.58579C5.21071 5.96086 5 6.46957 5 7V19C5 19.5304 5.21071 20.0391 5.58579 20.4142C5.96086 20.7893 6.46957 21 7 21H17C17.5304 21 18.0391 20.7893 18.4142 20.4142C18.7893 20.0391 19 19.5304 19 19V7C19 6.46957 18.7893 5.96086 18.4142 5.58579C18.0391 5.21071 17.5304 5 17 5H15M9 5C9 5.53043 9.21071 6.03914 9.58579 6.41421C9.96086 6.78929 10.4696 7 11 7H13C13.5304 7 14.0391 6.78929 14.4142 6.41421C14.7893 6.03914 15 5.53043 15 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="transition-opacity duration-300 group-[.collapsed]:opacity-0 whitespace-nowrap">Incoming Orders</span>
            </a>

            <a href="{{ route('manufacturer.catalog.index') }}" 
               class="flex items-center gap-3 px-3.5 py-3 text-sm text-white no-underline transition-colors {{ request()->routeIs('manufacturer.catalog.*') ? 'bg-white/10' : 'hover:bg-white/10' }}">
                <svg width="18" height="18" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20M4 19.5V5A2.5 2.5 0 0 1 6.5 2.5H20V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="transition-opacity duration-300 group-[.collapsed]:opacity-0 whitespace-nowrap">Catalog/Products</span>
            </a>

            <a href="{{ route('manufacturer.production.index') }}" 
               class="flex items-center gap-3 px-3.5 py-3 text-sm text-white no-underline transition-colors {{ request()->routeIs('manufacturer.production.*') ? 'bg-white/10' : 'hover:bg-white/10' }}">
                <svg width="18" height="18" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="transition-opacity duration-300 group-[.collapsed]:opacity-0 whitespace-nowrap">Production</span>
            </a>

            <a href="{{ route('manufacturer.payments.index') }}" 
               class="flex items-center gap-3 px-3.5 py-3 text-sm text-white no-underline transition-colors {{ request()->routeIs('manufacturer.payments.*') ? 'bg-white/10' : 'hover:bg-white/10' }}">
                <svg width="18" height="18" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 10H21M7 15H8M12 15H13M6 19H18C19.1046 19 20 18.1046 20 17V7C20 5.89543 19.1046 5 18 5H6C4.89543 5 4 5.89543 4 7V17C4 18.1046 4.89543 19 6 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="transition-opacity duration-300 group-[.collapsed]:opacity-0 whitespace-nowrap">Payments</span>
            </a>

            <a href="{{ route('manufacturer.connections.index') }}" 
               class="flex items-center gap-3 px-3.5 py-3 text-sm text-white no-underline transition-colors {{ request()->routeIs('manufacturer.connections.*') ? 'bg-white/10' : 'hover:bg-white/10' }}">
                <svg width="18" height="18" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="transition-opacity duration-300 group-[.collapsed]:opacity-0 whitespace-nowrap">Connections</span>
            </a>

            <a href="{{ route('manufacturer.reports.index') }}" 
               class="flex items-center gap-3 px-3.5 py-3 text-sm text-white no-underline transition-colors {{ request()->routeIs('manufacturer.reports.*') ? 'bg-white/10' : 'hover:bg-white/10' }}">
                <svg width="18" height="18" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 17V11M12 17V8M15 17V14M17 21H7C5.89543 21 5 20.1046 5 19V5C5 3.89543 5.89543 3 7 3H12.5858C12.851 3 13.1054 3.10536 13.2929 3.29289L18.7071 8.70711C18.8946 8.89464 19 9.149 19 9.41421V19C19 20.1046 18.1046 21 17 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="transition-opacity duration-300 group-[.collapsed]:opacity-0 whitespace-nowrap">Reports</span>
            </a>
        @else
            <!-- Shop Owner Navigation (Default) -->
            <a href="{{ route('shop.dashboard') }}" 
               class="flex items-center gap-3 px-3.5 py-3 text-sm text-white no-underline transition-colors {{ request()->routeIs('shop.dashboard') ? 'bg-white/10' : 'hover:bg-white/10' }}">
                <svg width="18" height="18" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="transition-opacity duration-300 group-[.collapsed]:opacity-0 whitespace-nowrap">Dashboard</span>
            </a>
            
            <a href="{{ route('shop.orders.create') }}" 
               class="flex items-center gap-3 px-3.5 py-3 text-sm text-white no-underline transition-colors {{ request()->routeIs('shop.orders.create') ? 'bg-white/10' : 'hover:bg-white/10' }}">
                <svg width="18" height="18" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="transition-opacity duration-300 group-[.collapsed]:opacity-0 whitespace-nowrap">Create Order</span>
            </a>

            <a href="{{ route('shop.orders.index') }}" 
               class="flex items-center gap-3 px-3.5 py-3 text-sm text-white no-underline transition-colors {{ request()->routeIs('shop.orders.index') ? 'bg-white/10' : 'hover:bg-white/10' }}">
                <svg width="18" height="18" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 5H7C6.46957 5 5.96086 5.21071 5.58579 5.58579C5.21071 5.96086 5 6.46957 5 7V19C5 19.5304 5.21071 20.0391 5.58579 20.4142C5.96086 20.7893 6.46957 21 7 21H17C17.5304 21 18.0391 20.7893 18.4142 20.4142C18.7893 20.0391 19 19.5304 19 19V7C19 6.46957 18.7893 5.96086 18.4142 5.58579C18.0391 5.21071 17.5304 5 17 5H15M9 5C9 5.53043 9.21071 6.03914 9.58579 6.41421C9.96086 6.78929 10.4696 7 11 7H13C13.5304 7 14.0391 6.78929 14.4142 6.41421C14.7893 6.03914 15 5.53043 15 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="transition-opacity duration-300 group-[.collapsed]:opacity-0 whitespace-nowrap">My Orders</span>
            </a>

            <a href="{{ route('shop.manufacturers') }}" 
               class="flex items-center gap-3 px-3.5 py-3 text-sm text-white no-underline transition-colors {{ request()->routeIs('shop.manufacturers') ? 'bg-white/10' : 'hover:bg-white/10' }}">
                <svg width="18" height="18" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="transition-opacity duration-300 group-[.collapsed]:opacity-0 whitespace-nowrap">Manufacturers</span>
            </a>

            <a href="{{ route('shop.payments') }}" 
               class="flex items-center gap-3 px-3.5 py-3 text-sm text-white no-underline transition-colors {{ request()->routeIs('shop.payments') ? 'bg-white/10' : 'hover:bg-white/10' }}">
                <svg width="18" height="18" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 10H21M7 15H8M12 15H13M6 19H18C19.1046 19 20 18.1046 20 17V7C20 5.89543 19.1046 5 18 5H6C4.89543 5 4 5.89543 4 7V17C4 18.1046 4.89543 19 6 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="transition-opacity duration-300 group-[.collapsed]:opacity-0 whitespace-nowrap">Payments</span>
            </a>

            <a href="{{ route('shop.reports') }}" 
               class="flex items-center gap-3 px-3.5 py-3 text-sm text-white no-underline transition-colors {{ request()->routeIs('shop.reports') ? 'bg-white/10' : 'hover:bg-white/10' }}">
                <svg width="18" height="18" class="shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 17V11M12 17V8M15 17V14M17 21H7C5.89543 21 5 20.1046 5 19V5C5 3.89543 5.89543 3 7 3H12.5858C12.851 3 13.1054 3.10536 13.2929 3.29289L18.7071 8.70711C18.8946 8.89464 19 9.149 19 9.41421V19C19 20.1046 18.1046 21 17 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="transition-opacity duration-300 group-[.collapsed]:opacity-0 whitespace-nowrap">Reports</span>
            </a>
        @endif
    </nav>

    <!-- Footer -->
    <div class="px-3.5 py-5 border-t border-white/10 shrink-0">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2 bg-transparent border-none text-white cursor-pointer text-sm opacity-70 hover:opacity-100 transition-opacity">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9M16 17L21 12M21 12L16 7M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="transition-opacity duration-300 group-[.collapsed]:opacity-0 whitespace-nowrap">Logout</span>
            </button>
        </form>
    </div>
</aside>
