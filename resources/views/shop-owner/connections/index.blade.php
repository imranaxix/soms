@extends('layouts.app')

@section('title', 'Manufacturers - SOMS')

@section('page_title', 'Manufacturers')
@section('page_subtitle', 'Manage your connections and search for new supply partners.')

@section('content')
    <!-- Search Section -->
    <div class="bg-white p-8 rounded-xl shadow-sm border border-neutral-100 mb-8 mt-4">
        <h2 class="text-lg font-bold text-neutral-900 mb-6">Add New Connection</h2>
        <form action="{{ route('connections.search') }}" method="GET" class="flex gap-3">
            <div class="flex-1 relative group">
                <input type="email" name="email" value="{{ request('email') }}" class="w-full pl-11 pr-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all group-hover:border-neutral-300" placeholder="Enter manufacturer's email (e.g., mfg@demo.com)" required>
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-hover:text-neutral-500 transition-colors" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <button type="submit" class="px-7 py-3.5 bg-primary-600 text-white rounded-lg font-bold hover:bg-primary-700 transition shadow-md shadow-primary-600/20">Find</button>
        </form>

        @if(session('error'))
            <div class="mt-6 p-4 rounded-lg border border-error-100 bg-error-50">
                <p class="text-sm text-error-600 font-bold">{{ session('error') }}</p>
            </div>
        @endif

        @if(session('searchUser'))
            @php 
                $user = session('searchUser'); 
                $displayName = data_get($user, 'business_name') ?: data_get($user, 'name');
                $initial = strtoupper(substr($displayName, 0, 1));
            @endphp
            <div class="mt-6">
                <a href="{{ route('user.show', data_get($user, 'id')) }}" class="block p-6 rounded-xl border border-primary-200 bg-primary-50/30 hover:bg-primary-50 hover:border-primary-300 transition-colors flex items-center justify-between cursor-pointer group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-primary-600 text-white flex items-center justify-center text-lg font-bold shadow-sm">{{ $initial }}</div>
                        <div>
                            <h3 class="text-base font-bold text-neutral-900 leading-tight group-hover:text-primary-700 transition-colors">{{ $displayName }}</h3>
                            <p class="text-xs text-neutral-500 mt-0.5">{{ data_get($user, 'email') }}</p>
                        </div>
                    </div>
                    <div class="px-5 py-2.5 bg-neutral-900 text-white rounded-lg font-bold text-sm group-hover:bg-neutral-800 transition-colors">View Profile</div>
                </a>
            </div>
        @endif
    </div>

    <!-- Pending Requests Section -->
    @if($pendingRequests->count() > 0)
    <div class="bg-white p-8 rounded-xl shadow-sm border border-neutral-100 overflow-hidden mb-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-lg font-bold text-neutral-900">Pending Requests</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pendingRequests as $request)
            @php $partner = $request->manufacturer; @endphp
            <div class="p-6 rounded-xl border border-warning-200 bg-warning-50/30 hover:bg-white hover:border-warning-300 hover:shadow-md transition-all group">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 rounded-full bg-warning-500 text-white flex items-center justify-center text-xl font-bold shadow-sm">
                        {{ strtoupper(substr($partner->business_name ?? $partner->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-neutral-900 leading-tight">{{ $partner->business_name ?? $partner->name }}</h3>
                        <p class="text-xs text-neutral-500 mt-0.5">{{ $partner->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <form action="{{ route('connections.accept', $request->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button class="w-full py-2.5 bg-success-600 text-white rounded-lg text-sm font-bold hover:bg-success-700 transition">Accept</button>
                    </form>
                    <form action="{{ route('connections.reject', $request->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button class="w-full py-2.5 bg-white border border-error-200 text-error-600 rounded-lg text-sm font-bold hover:bg-error-50 transition">Reject</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Connections Section -->
    <div class="bg-white p-8 rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-lg font-bold text-neutral-900">My Connections</h2>
        </div>
        
        @if($activeConnections->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($activeConnections as $connection)
            @php $partner = $connection->manufacturer; @endphp
            <div class="p-6 rounded-2xl border border-neutral-100 bg-white hover:border-primary-200 hover:shadow-lg transition-all group relative">
                <!-- 3 Dot Dropdown -->
                <div class="absolute top-4 right-4">
                    <button onclick="document.getElementById('connDropdown-{{ $connection->id }}').classList.toggle('hidden')" class="w-8 h-8 rounded-full text-neutral-400 hover:bg-neutral-100 hover:text-neutral-600 flex items-center justify-center transition focus:outline-none">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 12h.01M12 6h.01M12 18h.01" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <div id="connDropdown-{{ $connection->id }}" class="hidden absolute right-0 mt-1 w-40 bg-white border border-neutral-200 rounded-xl shadow-xl z-20 overflow-hidden">
                        <form action="{{ route('connections.destroy', $connection->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this connection?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-left px-4 py-3 text-sm text-error-600 font-bold hover:bg-error-50 flex items-center gap-2 transition-colors">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 7L18.1327 19.1425C18.0579 20.1891 17.187 21 16.1378 21H7.86224C6.81296 21 5.94208 20.1891 5.86732 19.1425L5 7M10 11V17M14 11V17M15 7V4C15 3.44772 14.5523 3 14 3H10C9.44772 3 9 3.44772 9 4V7M4 7H20" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Remove
                            </button>
                        </form>
                    </div>
                </div>

                <div class="flex items-center gap-4 mb-6 pt-2">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-2xl font-black shadow-md shrink-0">
                        {{ strtoupper(substr($partner->business_name ?? $partner->name, 0, 1)) }}
                    </div>
                    <div class="pr-6 overflow-hidden">
                        <h3 class="text-lg font-black text-neutral-900 leading-tight truncate">{{ $partner->business_name ?? $partner->name }}</h3>
                        <p class="text-sm font-medium text-neutral-500 mt-1 truncate" title="{{ $partner->email }}">{{ $partner->email }}</p>
                    </div>
                </div>
                <div class="pt-4 border-t border-neutral-100 flex gap-3">
                    <a href="{{ route('user.show', $partner->id) }}" class="flex-1 text-center py-3 bg-primary-50 text-primary-700 rounded-xl text-sm font-bold hover:bg-primary-100 transition shadow-sm border border-primary-100">View Profile</a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-neutral-500 font-medium">You don't have any connections yet.</p>
        </div>
        @endif
    </div>

    <script>
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('div[id^="connDropdown-"]');
            dropdowns.forEach(dropdown => {
                const button = dropdown.previousElementSibling;
                if (!dropdown.contains(event.target) && !button.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
