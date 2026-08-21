@extends('layouts.app')

@section('title', 'Users - Admin')
@section('page_title', 'Users')
@section('page_subtitle', 'Manage platform users')

@section('content')
    @if(session('success'))
        <div class="flex items-center gap-3 px-5 py-4 mb-6 bg-success-50 border border-success-200 text-success-700 rounded-xl text-sm font-medium">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 13.01 9 10.01"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-5 py-4 mb-6 bg-error-50 border border-error-200 text-error-700 rounded-xl text-sm font-medium">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Filter Tabs -->
    <div class="flex flex-wrap items-center gap-3 mb-6 mt-4">
        @foreach(['all' => 'All', 'shop_owner' => 'Shop Owners', 'manufacturer' => 'Manufacturers', 'admin' => 'Admins', 'suspended' => 'Suspended', 'unverified' => 'Unverified'] as $key => $label)
        <a href="{{ route('admin.users', ['role' => in_array($key, ['shop_owner', 'manufacturer', 'admin'], true) ? $key : null, 'status' => in_array($key, ['suspended', 'unverified'], true) ? $key : null]) }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold transition
            {{ (request('role') === $key || request('status') === $key || ($key === 'all' && !request('role') && !request('status')))
                ? 'bg-primary-600 text-white shadow-sm'
                : 'bg-white text-neutral-500 border border-neutral-200 hover:bg-neutral-50' }}">
            {{ $label }}
            <span class="ml-1 opacity-70">{{ $counts[$key] }}</span>
        </a>
        @endforeach
    </div>

    <!-- Search -->
    <form method="GET" action="{{ route('admin.users') }}" class="mb-6">
        <div class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or business..." 
                class="flex-1 px-4 py-2.5 bg-white border border-neutral-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500">
            @if(request('role'))<input type="hidden" name="role" value="{{ request('role') }}">@endif
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition">Search</button>
        </div>
    </form>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-neutral-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-primary-600 text-[13px] font-medium text-white uppercase tracking-wider">
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Business</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Joined</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-neutral-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center font-bold uppercase text-xs shrink-0">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-neutral-900">{{ $user->name }}</p>
                                    <p class="text-xs text-neutral-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-600">{{ $user->business_name ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full {{ $user->role === 'manufacturer' ? 'bg-blue-50 text-blue-600' : ($user->role === 'admin' ? 'bg-neutral-100 text-neutral-600' : 'bg-green-50 text-green-600') }}">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full w-fit {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $user->is_active ? 'Active' : 'Suspended' }}
                                </span>
                                @if($user->role === 'manufacturer')
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full w-fit {{ $user->is_verified ? 'bg-blue-50 text-blue-600' : 'bg-warning-100 text-warning-600' }}">
                                    {{ $user->is_verified ? 'Verified' : 'Unverified' }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-neutral-500">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-xs font-bold text-primary-600 hover:text-primary-800 bg-primary-50 px-3 py-1.5 rounded-lg transition">View</a>
                                @if($user->role !== 'admin')
                                <form action="{{ route('admin.users.toggle-active', $user->id) }}" method="POST" onsubmit="return confirm('{{ $user->is_active ? 'Suspend' : 'Activate' }} {{ $user->name }}?');">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg transition {{ $user->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                                        {{ $user->is_active ? 'Suspend' : 'Activate' }}
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-neutral-400">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-neutral-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>
@endsection