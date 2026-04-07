<header class="bg-white px-6 py-5 border-b border-neutral-200 flex items-center justify-between shadow-sm">
    <div>
        <h1 class="text-2xl font-bold text-neutral-900 leading-tight">@yield('page_title')</h1>
        <p class="text-sm text-neutral-500 mt-1">@yield('page_subtitle')</p>
    </div>
    <div class="flex items-center gap-3">
        @yield('header_actions')
    </div>
</header>
