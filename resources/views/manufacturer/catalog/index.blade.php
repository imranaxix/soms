@extends('layouts.app')

@section('title', 'Product Catalog - Manufacturer')

@section('page_title', 'Product Catalog')
@section('page_subtitle', 'Manage the products and sub-products you offer')

@section('header_actions')
    <a href="{{ route('manufacturer.catalog.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors shadow-sm">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Create New Product
    </a>
@endsection

@section('content')
<div class="space-y-6">
    

    <!-- Products Table -->
    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden mt-8">
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
                                <form action="{{ route('manufacturer.catalog.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-1.5 bg-error-500 text-white text-[10px] font-bold rounded shadow-sm hover:bg-error-600 transition-all uppercase">Delete</button>
                                </form>
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
