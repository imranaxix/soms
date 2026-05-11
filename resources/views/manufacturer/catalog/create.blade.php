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
    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-8 mt-8 mx-auto max-w-3xl">
        <h2 class="text-lg font-bold text-neutral-900 mb-8 border-b border-neutral-100 pb-4">Add New Product</h2>
        
        <form action="{{ route('manufacturer.catalog.store') }}" method="POST" class="space-y-8 max-w-2xl" enctype="multipart/form-data">
            @csrf
            <!-- Product Name -->
            <div class="space-y-2">
                <label class="block text-sm font-bold text-neutral-700">Product Name <span class="text-error-500 font-bold">*</span></label>
                <input type="text" name="name" required placeholder="e.g., T-Shirts" class="w-full bg-neutral-50 border border-neutral-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label class="block text-sm font-bold text-neutral-700">Description</label>
                <textarea name="description" rows="4" placeholder="Brief description of the product" class="w-full bg-neutral-50 border border-neutral-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"></textarea>
            </div>

            <!-- Variations -->
            <div class="space-y-4">
                <label class="block text-sm font-bold text-neutral-700">Sub-Products (Variations/Types)</label>
                <div id="variations-container" class="space-y-4">
                    <div class="variation-row bg-neutral-50 p-4 rounded-xl border border-neutral-200 relative">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-neutral-700">Variant Name <span class="text-error-500">*</span></label>
                                <input type="text" name="variations[0][name]" required placeholder="e.g., Red - Large" class="w-full bg-white border border-neutral-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-neutral-700">Price (Optional)</label>
                                <input type="number" step="0.01" name="variations[0][price]" placeholder="0.00" class="w-full bg-white border border-neutral-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-neutral-700">Stock (Optional)</label>
                                <input type="number" name="variations[0][stock_quantity]" placeholder="0" class="w-full bg-white border border-neutral-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 transition-all">
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-xs font-bold text-neutral-700">Image (Optional)</label>
                                <input type="file" name="variations[0][image]" accept="image/*" class="w-full bg-white border border-neutral-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 transition-all">
                            </div>
                        </div>
                        <button type="button" class="remove-variation absolute -top-3 -right-3 w-8 h-8 bg-white border border-neutral-200 text-error-500 rounded-full hover:bg-error-50 hover:border-error-200 transition-all flex items-center justify-center shadow-sm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="button" class="add-variation inline-flex items-center gap-2 px-4 py-2 bg-primary-50 text-primary-600 rounded-lg hover:bg-primary-100 transition-colors text-sm font-bold w-full justify-center border border-primary-200 border-dashed">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Add Another Sub-Product
                </button>
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
                            <td class="px-6 py-4 font-bold text-neutral-900">{{ $product->name }}</td>
                            <td class="px-6 py-4 text-sm text-neutral-500">{{ $product->description ?: '--' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($product->variants as $variant)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-tighter">
                                            {{ $variant->variant_name }}
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('variations-container');
        let variantIndex = 1;
        
        document.body.addEventListener('click', function(e) {
            const addBtn = e.target.closest('.add-variation');
            const removeBtn = e.target.closest('.remove-variation');
            
            if (addBtn) {
                const newRow = document.createElement('div');
                newRow.className = 'variation-row bg-neutral-50 p-4 rounded-xl border border-neutral-200 relative mt-4';
                newRow.innerHTML = `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-neutral-700">Variant Name <span class="text-error-500">*</span></label>
                                <input type="text" name="variations[${variantIndex}][name]" required placeholder="e.g., Red - Large" class="w-full bg-white border border-neutral-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-neutral-700">Price (Optional)</label>
                                <input type="number" step="0.01" name="variations[${variantIndex}][price]" placeholder="0.00" class="w-full bg-white border border-neutral-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-neutral-700">Stock (Optional)</label>
                                <input type="number" name="variations[${variantIndex}][stock_quantity]" placeholder="0" class="w-full bg-white border border-neutral-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 transition-all">
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-xs font-bold text-neutral-700">Image (Optional)</label>
                                <input type="file" name="variations[${variantIndex}][image]" accept="image/*" class="w-full bg-white border border-neutral-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 transition-all">
                            </div>
                        </div>
                        <button type="button" class="remove-variation absolute -top-3 -right-3 w-8 h-8 bg-white border border-neutral-200 text-error-500 rounded-full hover:bg-error-50 hover:border-error-200 transition-all flex items-center justify-center shadow-sm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                `;
                container.appendChild(newRow);
                variantIndex++;
            }
            
            if (removeBtn) {
                const row = removeBtn.closest('.variation-row');
                if (container.children.length > 1) {
                    row.remove();
                } else {
                    alert('You must have at least one variant.');
                }
            }
        });
    });
</script>
@endsection
