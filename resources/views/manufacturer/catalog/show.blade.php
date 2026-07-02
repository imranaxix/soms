@extends('layouts.app')

@section('title', $product->name . ' - Product Details')

@section('page_title', 'Product Details')
@section('page_subtitle', 'Viewing details and sub-products for ' . $product->name)

@section('header_actions')
    <a href="{{ route('manufacturer.catalog.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-neutral-100 text-neutral-700 rounded-lg font-medium hover:bg-neutral-200 transition-colors shadow-sm">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Back to Catalog
    </a>
@endsection

@section('content')
<div class="space-y-8 mt-8">
    
    <!-- Product Summary -->
    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-8">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900">{{ $product->name }}</h1>
                <p class="mt-2 text-neutral-600">{{ $product->description ?: 'No description provided.' }}</p>
            </div>
            <div class="text-right space-y-3 flex flex-col items-end">
                <a href="{{ route('manufacturer.catalog.edit', $product->id) }}" class="inline-flex items-center gap-2 px-4 py-1.5 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition-colors shadow-sm text-xs uppercase">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Edit Product
                </a>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary-50 text-primary-600 border border-primary-100">
                    {{ $product->variants->count() }} Variants
                </span>
            </div>
        </div>
    </div>

    <!-- Variants Grid -->
    <div>
        <h2 class="text-lg font-bold text-neutral-900 mb-4">Sub-Products (Variants)</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($product->variants as $variant)
                <div class="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                    
                    @if($variant->image)
                        <div class="h-48 w-full bg-neutral-100 border-b border-neutral-100 overflow-hidden relative">
                            <img src="{{ Storage::url($variant->image) }}" alt="{{ $variant->variant_name }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="h-48 w-full bg-neutral-50 border-b border-neutral-100 flex items-center justify-center">
                            <svg class="w-12 h-12 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif

                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="text-md font-bold text-neutral-900 mb-1">{{ $variant->variant_name }}</h3>
                        
                        <div class="mt-auto space-y-3 pt-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-neutral-500">SKU</span>
                                <span class="font-mono text-neutral-900 bg-neutral-100 px-2 py-0.5 rounded text-xs">{{ $variant->sku }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center text-sm border-t border-neutral-50 pt-2">
                                <span class="text-neutral-500">Price</span>
                                <span class="font-bold text-neutral-900">
                                    @if($variant->price > 0)
                                        Rs. {{ number_format($variant->price, 2) }}
                                    @else
                                        <span class="text-neutral-400 italic font-normal">Not Set</span>
                                    @endif
                                </span>
                            </div>
                            
                            <div class="flex justify-between items-center text-sm border-t border-neutral-50 pt-2">
                                <span class="text-neutral-500">Stock Available</span>
                                <span class="font-bold {{ $variant->stock_quantity > 0 ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $variant->stock_quantity }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-xl border border-neutral-200 border-dashed">
                    <p class="text-neutral-500 font-medium">No variations found for this product.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
