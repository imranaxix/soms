@extends('layouts.app')

@section('title', 'Create New Order - SOMS')

@section('page_title', 'Create New Order')
@section('page_subtitle', 'Fill in the details to create a new order')

@section('content')
    <div class="max-w-3xl mx-auto mt-4 px-0">
        <div class="bg-white rounded-xl shadow-sm border border-neutral-100 p-8">
            <form action="{{ route('shop.orders.store') }}" method="POST">
                @csrf
                <!-- Product Details -->
                <div class="mb-10 block">
                    <h3 class="text-[17px] font-bold text-neutral-900 border-b-2 border-neutral-100 pb-3 mb-6 transition-colors">Product Details</h3>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-neutral-700 mb-2">Select Manufacturer</label>
                        <select id="manufacturer-select" class="w-full px-4 py-3 pr-14 bg-neutral-50 border border-neutral-200 rounded-lg text-sm outline-none hover:border-neutral-300 transition-colors" name="manufacturer_id" required>
                            <option value="">-- Select Manufacturer --</option>
                            @foreach($manufacturers as $m)
                                <option value="{{ $m->id }}">{{ $m->business_name ?? $m->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-neutral-700 mb-2">Select Product</label>
                        <select id="product-select" class="w-full px-4 py-3 pr-14 bg-neutral-50 border border-neutral-200 rounded-lg text-sm outline-none hover:border-neutral-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" name="product_id" disabled required>
                            <option value="">-- Select Product --</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-neutral-700 mb-2">Select Variant</label>
                        <select id="variant-select" class="w-full px-4 py-3 pr-14 bg-neutral-50 border border-neutral-200 rounded-lg text-sm outline-none hover:border-neutral-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" name="variant_id" disabled required>
                            <option value="">-- Select Variant --</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-neutral-700 mb-2 font-poppins">Quantity</label>
                            <input type="number" class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none hover:border-neutral-300 transition-colors" name="quantity" placeholder="100" min="1" required>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-neutral-700 mb-2">Unit</label>
                           <select class="w-full px-4 py-3 pr-14 bg-neutral-50 border border-neutral-200 rounded-lg text-sm outline-none hover:border-neutral-300" name="unit">
                                <option value="pieces">Pieces</option>
                                <option value="meters">Meters</option>
                                <option value="kilograms">Kilograms</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="mb-10">
                    <h3 class="text-[17px] font-bold text-neutral-900 border-b-2 border-neutral-100 pb-3 mb-6">Order Details</h3>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-neutral-700 mb-2">Due Date</label>
                            <input type="date" class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none hover:border-neutral-300 transition-colors" name="due_date" required>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-neutral-700 mb-2">Total Amount (Rs)</label>
                            <input type="number" id="total-amount" class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-lg text-sm font-semibold text-neutral-800 placeholder:font-normal placeholder:text-neutral-400 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none hover:border-neutral-300 transition-colors" name="total_amount" placeholder="10000" required>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-neutral-700 mb-2">Payment Terms</label>
                        <select class="w-full px-4 py-3 pr-14 bg-neutral-50 border border-neutral-200 rounded-lg text-sm outline-none hover:border-neutral-300" name="payment_terms">
                            <option value="full_advance">Full Advance</option>
                            <option value="50_advance">50% Advance, 50% on Delivery</option>
                            <option value="on_delivery">Full Payment on Delivery</option>
                        </select>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="mb-10">
                    <h3 class="text-[17px] font-bold text-neutral-900 border-b-2 border-neutral-100 pb-3 mb-6">Additional Information</h3>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-neutral-700 mb-2">Special Instructions</label>
                        <textarea class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none hover:border-neutral-300 transition-colors min-h-30 resize-y" name="special_instructions" rows="4" placeholder="Any special requirements..."></textarea>
                    </div>
                </div>

                <div class="flex gap-4 pt-8 border-t border-neutral-100">
                    <button type="submit" class="flex-1 px-6 py-3 bg-primary-600 text-white rounded-lg font-bold hover:bg-primary-700 transition shadow-md shadow-primary-600/20">Create Order</button>
                    <a href="{{ route('shop.dashboard') }}" class="px-6 py-3 bg-white border border-neutral-200 text-neutral-700 rounded-lg font-bold hover:bg-neutral-50 transition">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mSelect = document.getElementById('manufacturer-select');
            const pSelect = document.getElementById('product-select');
            const vSelect = document.getElementById('variant-select');
            const amountInput = document.getElementById('total-amount');

            mSelect.addEventListener('change', async function() {
                const mId = this.value;
                pSelect.innerHTML = '<option value="">-- Select Product --</option>';
                vSelect.innerHTML = '<option value="">-- Select Variant --</option>';
                pSelect.disabled = true;
                vSelect.disabled = true;

                if (mId) {
                    try {
                        const response = await fetch(`/shop/api/manufacturers/${mId}/products`);
                        const products = await response.json();
                        
                        products.forEach(p => {
                            const option = document.createElement('option');
                            option.value = p.id;
                            option.textContent = p.name;
                            pSelect.appendChild(option);
                        });
                        
                        pSelect.disabled = false;
                    } catch (error) {
                        console.error('Error fetching products:', error);
                    }
                }
            });

            pSelect.addEventListener('change', async function() {
                const pId = this.value;
                vSelect.innerHTML = '<option value="">-- Select Variant --</option>';
                vSelect.disabled = true;

                if (pId) {
                    try {
                        const response = await fetch(`/shop/api/products/${pId}/variants`);
                        const variants = await response.json();
                        
                        variants.forEach(v => {
                            const option = document.createElement('option');
                            option.value = v.id;
                            option.textContent = `${v.variant_name} (${v.sku || 'No SKU'}) - Rs ${v.price}`;
                            option.dataset.price = v.price;
                            vSelect.appendChild(option);
                        });
                        
                        vSelect.disabled = false;
                    } catch (error) {
                        console.error('Error fetching variants:', error);
                    }
                }
            });

            vSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.dataset.price) {
                    const quantity = document.querySelector('input[name="quantity"]').value || 1;
                    amountInput.value = selectedOption.dataset.price * quantity;
                }
            });

            document.querySelector('input[name="quantity"]').addEventListener('input', function() {
                const selectedOption = vSelect.options[vSelect.selectedIndex];
                if (selectedOption && selectedOption.dataset.price) {
                    amountInput.value = selectedOption.dataset.price * this.value;
                }
            });
        });
    </script>
@endsection
