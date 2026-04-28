@extends('layouts.guest')

@section('title', 'Register - SOMS')

@section('content')
<div class="flex min-h-screen">
    <div class="hidden lg:flex lg:w-1/2 auth-gradient items-center justify-center p-12 relative overflow-hidden">
        <div class="absolute top-0 -left-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -right-20 w-80 h-80 bg-primary-400/20 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 text-center max-w-lg">
            <div class="mb-10 inline-flex items-center justify-center w-24 h-24 bg-white/10 glass-panel rounded-3xl">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="text-white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">Supplier Order Management</h1>
            <p class="text-primary-100 text-lg lg:text-xl font-medium opacity-90 leading-relaxed">
                Create your account and start managing orders efficiently.
            </p>
        </div>
        
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-primary-200/50 text-sm font-medium">
            © 2026 SOMS Platform. All rights reserved.
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-white overflow-y-auto">
        <div class="w-full max-w-md my-auto">
            <div class="lg:hidden flex items-center gap-3 mb-10">
                <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="text-white" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-neutral-900">SOMS</span>
            </div>

            <div class="mb-8">
                <h2 class="text-3xl font-bold text-neutral-900 mb-2">Create Account</h2>
                <p class="text-neutral-500 font-medium">Fill in your details to get started</p>
            </div>

            

            <form action="{{ route('register') }}" method="POST" class="space-y-5">
                @csrf
               <div class="flex p-1 bg-neutral-100 rounded-xl mb-8">
                    {{-- Shop Owner Button --}}
                    <button type="button" id="role-shop" 
                        class="flex-1 py-2.5 text-sm font-bold rounded-lg transition-all 
                        {{ old('role', 'shop_owner') === 'shop_owner' 
                            ? 'bg-white text-primary-600 shadow-sm border border-primary-100' 
                            : 'text-neutral-500 hover:text-neutral-700' 
                        }}">
                        Shop Owner
                    </button>

                    {{-- Manufacturer Button --}}
                    <button type="button" id="role-manufacturer" 
                        class="flex-1 py-2.5 text-sm font-bold rounded-lg transition-all 
                        {{ old('role') === 'manufacturer' 
                            ? 'bg-white text-primary-600 shadow-sm border border-primary-100' 
                            : 'text-neutral-500 hover:text-neutral-700' 
                        }}">
                        Manufacturer
                    </button>

                    <input type="hidden" name="role" id="role-input" value="{{ old('role', 'shop_owner') }}">
                 </div>
                <div>
                    <label for="name" class="block text-sm font-semibold text-neutral-700 mb-1.5">Full Name</label>
                    <input type="text" id="name" name="name"  value="{{ old('name') }}"
                        placeholder="John Doe"
                        class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all placeholder:text-neutral-400">
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="business_name" class="block text-sm font-semibold text-neutral-700 mb-1.5">Business Name</label>
                    <input type="text" id="business_name" name="business_name"  value="{{ old('business_name') }}"
                        placeholder="Your Company Ltd."
                        class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all placeholder:text-neutral-400">
                    @error('business_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-neutral-700 mb-1.5">Email Address</label>
                    <input type="email" id="email" name="email"  value="{{ old('email') }}"
                        placeholder="you@company.com"
                        class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all placeholder:text-neutral-400">
                    @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-neutral-700 mb-1.5">Password</label>
                        <input type="password" id="password" name="password"  
                            placeholder="••••••••"
                            class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all placeholder:text-neutral-400">
                            @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-neutral-700 mb-1.5">Confirm</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"  
                            placeholder="••••••••"
                            class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all placeholder:text-neutral-400">
                            @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-4 bg-primary-600 text-white rounded-xl font-bold text-lg hover:bg-primary-700 active:scale-[0.98] transition-all shadow-lg shadow-primary-500/25">
                        Create Account
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center border-t border-neutral-100 pt-8">
                <p class="text-neutral-500 font-medium">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-primary-600 font-bold hover:text-primary-700 transition-colors ml-1">Login</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    const shopBtn = document.getElementById('role-shop');
    const manufacturerBtn = document.getElementById('role-manufacturer');
    const roleInput = document.getElementById('role-input');

    const activeClasses = ['bg-white', 'text-primary-600', 'shadow-sm', 'border', 'border-primary-100'];
    const inactiveClasses = ['text-neutral-500', 'hover:text-neutral-700'];

    shopBtn.addEventListener('click', () => {
        shopBtn.classList.add(...activeClasses);
        shopBtn.classList.remove(...inactiveClasses);
        manufacturerBtn.classList.remove(...activeClasses);
        manufacturerBtn.classList.add(...inactiveClasses);
        roleInput.value = 'shop_owner';
    });

    manufacturerBtn.addEventListener('click', () => {
        manufacturerBtn.classList.add(...activeClasses);
        manufacturerBtn.classList.remove(...inactiveClasses);
        shopBtn.classList.remove(...activeClasses);
        shopBtn.classList.add(...inactiveClasses);
        roleInput.value = 'manufacturer';
    });
</script>
@endsection
