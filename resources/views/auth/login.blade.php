@extends('layouts.guest')

@section('title', 'Login - SOMS')

@section('content')
<div class="flex min-h-screen">
    <div class="hidden lg:flex lg:w-1/2 auth-gradient items-center justify-center p-12 relative overflow-hidden">
        <div class="absolute top-0 -left-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -right-20 w-80 h-80 bg-primary-400/20 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 text-center max-w-lg">
            <div class="mb-10 inline-flex items-center justify-center w-24 h-24 bg-white/10 glass-panel rounded-3xl group transition-all duration-500 hover:scale-110">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="text-white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">Supplier Order Management</h1>
            <p class="text-primary-100 text-lg lg:text-xl font-medium opacity-90 leading-relaxed">
                Streamline your manufacturing orders and payments in one powerful platform.
            </p>
        </div>
        
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-primary-200/50 text-sm font-medium">
            © 2026 SOMS Platform. All rights reserved.
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-white">
        <div class="w-full max-w-md">
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

            <div class="mb-10">
                <h2 class="text-3xl font-bold text-neutral-900 mb-3">Welcome Back</h2>
                <p class="text-neutral-500 font-medium">Enter your credentials to access your account</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-semibold text-neutral-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required 
                        placeholder="you@company.com"
                        class="w-full px-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all placeholder:text-neutral-400">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label for="password" class="block text-sm font-semibold text-neutral-700">Password</label>
                        <a href="#" class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">Forgot password?</a>
                    </div>
                    <input type="password" id="password" name="password" required 
                        placeholder="••••••••"
                        class="w-full px-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all placeholder:text-neutral-400">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500 cursor-pointer">
                    <label for="remember" class="ml-2 block text-sm font-medium text-neutral-600 cursor-pointer select-none">Remember me</label>
                </div>

                <button type="submit" class="w-full py-4 bg-primary-600 text-white rounded-xl font-bold text-lg hover:bg-primary-700 active:scale-[0.98] transition-all shadow-lg shadow-primary-500/25">
                    Sign In
                </button>
            </form>

            <div class="mt-10 text-center">
                <p class="text-neutral-500 font-medium">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-primary-600 font-bold hover:text-primary-700 transition-colors ml-1">Create Account</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
