<header class="bg-white px-6 py-5 border-b border-neutral-200 flex items-center justify-between shadow-sm">
    <div class="flex items-center gap-4">
        @if(View::hasSection('back_link'))
            <a href="@yield('back_link')" class="w-10 h-10 flex items-center justify-center bg-white border border-neutral-200 rounded-lg text-neutral-600 hover:bg-neutral-50 transition shadow-sm">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        @endif
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 leading-tight">@yield('page_title')</h1>
            @if(View::hasSection('page_subtitle'))
                <p class="text-sm text-neutral-500 mt-1">@yield('page_subtitle')</p>
            @endif
        </div>
    </div>
    <div class="flex items-center gap-3">
        @yield('header_actions')
    </div>
</header>
