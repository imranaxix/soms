@extends('layouts.app')

@section('title', 'Add New Product - Manufacturer')
@section('page_title', 'Add New Product')
@section('page_subtitle', 'Create a new product and add variations')

@section('header_actions')
    <a href="{{ route('manufacturer.catalog.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors shadow-sm">
        Cancel
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Add Product Form -->
    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-8 mt-8">
        <h2 class="text-lg font-bold text-neutral-900 mb-8 border-b border-neutral-100 pb-4">Add New Product</h2>
        
        <form action="#" method="POST" class="space-y-8 max-w-2xl">
            <!-- Product Name -->
            <div class="space-y-2">
                <label class="block text-sm font-bold text-neutral-700">Product Name <span class="text-error-500 font-bold">*</span></label>
                <input type="text" placeholder="e.g., T-Shirts" class="w-full bg-neutral-50 border border-neutral-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label class="block text-sm font-bold text-neutral-700">Description</label>
                <textarea rows="4" placeholder="Brief description of the product" class="w-full bg-neutral-50 border border-neutral-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"></textarea>
            </div>

            <!-- Variations -->
            <div class="space-y-4">
                <label class="block text-sm font-bold text-neutral-700">Sub-Products (Variations/Types)</label>
                <div class="space-y-3">
                    <input type="text" placeholder="e.g., V Neck, Round Neck, Polo" class="w-full bg-neutral-50 border border-neutral-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                    <button type="button" class="inline-flex items-center gap-2 text-xs font-bold text-neutral-700 bg-neutral-100 px-3 py-2 rounded border border-neutral-200 hover:bg-neutral-200 transition-all">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Add Variation
                    </button>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-6">
                <button type="submit" class="px-8 py-3 bg-primary-600 text-white text-sm font-bold rounded-lg shadow-md hover:bg-primary-700 transition-all active:scale-95 shadow-primary-200">
                    Save Product
                </button>
            </div>
        </form>
    </div>

    <!-- Already Existing Products Table -->
    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden mt-12">
        <div class="p-6 border-b border-neutral-100">
            <h2 class="text-lg font-bold text-neutral-900">Your Products</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-primary-600 text-white">
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Description</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Variations / Sub-Products</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-neutral-900">{{ $product['name'] }}</td>
                            <td class="px-6 py-4 text-sm text-neutral-500">{{ $product['description'] ?: '--' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($product['variations'] as $variation)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-tighter">
                                            {{ $variation }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="px-4 py-1.5 bg-error-500 text-white text-[10px] font-bold rounded shadow-sm hover:bg-error-600 transition-all uppercase">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-neutral-500 font-medium">No products registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
