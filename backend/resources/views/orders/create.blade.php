@extends('layouts.app')

@section('title', 'Create Order')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create Order - Table {{ $table->table_number }}</h1>
        <p class="text-gray-600">Add items to the order</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Menu Items -->
        <div class="lg:col-span-2">
            <!-- Category Tabs -->
            <div class="bg-white rounded-lg shadow mb-4">
                <div class="flex overflow-x-auto p-2 space-x-2">
                    <button onclick="filterCategory('all')" 
                            class="category-btn px-4 py-2 text-sm font-medium rounded-md whitespace-nowrap bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                        All Items
                    </button>
                    @foreach($categories as $category)
                        <button onclick="filterCategory('{{ $category->slug }}')" 
                                class="category-btn px-4 py-2 text-sm font-medium rounded-md whitespace-nowrap bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Menu Items Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($menuItems as $item)
                    <div class="menu-item bg-white rounded-lg shadow p-4 {{ $item->is_available ? 'cursor-pointer hover:shadow-lg transition' : 'opacity-50' }}" 
                         data-category="{{ $item->category->slug }}"
                         @if($item->is_available)
                         onclick="addToCart({{ $item->id }}, '{{ $item->name }}', {{ $item->price }})"
                         @endif>
                        <div class="flex items-start space-x-4">
                            <div class="w-20 h-20 bg-gray-200 rounded flex-shrink-0">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover rounded">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-400 to-purple-500 rounded">
                                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $item->name }}</h3>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ $item->description }}</p>
                                <p class="text-lg font-bold text-indigo-600 mt-1">₹{{ number_format($item->price, 2) }}</p>
                                @if(!$item->is_available)
                                    <span class="text-xs text-red-600 font-medium">Unavailable</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Order Cart -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow sticky top-6">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Order Summary</h2>
                    <p class="text-sm text-gray-600">Table: {{ $table->table_number }}</p>
                </div>

                <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                    @csrf
                    <input type="hidden" name="restaurant_table_id" value="{{ $table->id }}">
                    
                    <div class="p-4 max-h-96 overflow-y-auto" id="cartItems">
                        <p class="text-gray-500 text-center py-8">No items added yet</p>
                    </div>

                    <div class="p-4 border-t border-gray-200 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-semibold" id="subtotal">₹0.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax (10%):</span>
                            <span class="font-semibold" id="tax">₹0.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Service Charge (5%):</span>
                            <span class="font-semibold" id="serviceCharge">₹0.00</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 border-t">
                            <span>Total:</span>
                            <span class="text-indigo-600" id="total">₹0.00</span>
                        </div>
                    </div>

                    <div class="p-4 border-t border-gray-200">
                        <textarea name="notes" rows="2" placeholder="Special instructions (optional)" 
                                  class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm mb-3"></textarea>
                        
                        <button type="submit" id="submitBtn" disabled
                                class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3 px-4 rounded transition">
                            Place Order
                        </button>
                        <a href="{{ route('tables.index') }}" 
                           class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-4 rounded mt-2 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];

function filterCategory(category) {
    const items = document.querySelectorAll('.menu-item');
    const buttons = document.querySelectorAll('.category-btn');
    
    buttons.forEach(btn => {
        btn.classList.remove('bg-indigo-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    event.target.classList.remove('bg-gray-200', 'text-gray-700');
    event.target.classList.add('bg-indigo-600', 'text-white');
    
    items.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function addToCart(id, name, price) {
    const existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ id, name, price, quantity: 1 });
    }
    
    updateCart();
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    updateCart();
}

function updateQuantity(id, change) {
    const item = cart.find(item => item.id === id);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(id);
        } else {
            updateCart();
        }
    }
}

function updateCart() {
    const cartContainer = document.getElementById('cartItems');
    const submitBtn = document.getElementById('submitBtn');
    
    if (cart.length === 0) {
        cartContainer.innerHTML = '<p class="text-gray-500 text-center py-8">No items added yet</p>';
        submitBtn.disabled = true;
    } else {
        let html = '<div class="space-y-3">';
        cart.forEach(item => {
            html += `
                <div class="flex items-center justify-between border-b pb-3">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">${item.name}</p>
                        <p class="text-sm text-gray-600">₹${item.price.toFixed(2)} each</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="updateQuantity(${item.id}, -1)" 
                                class="w-6 h-6 bg-gray-200 rounded hover:bg-gray-300">-</button>
                        <span class="w-8 text-center font-semibold">${item.quantity}</span>
                        <button type="button" onclick="updateQuantity(${item.id}, 1)" 
                                class="w-6 h-6 bg-gray-200 rounded hover:bg-gray-300">+</button>
                        <button type="button" onclick="removeFromCart(${item.id})" 
                                class="ml-2 text-red-600 hover:text-red-800">×</button>
                    </div>
                </div>
                <input type="hidden" name="items[${item.id}][menu_item_id]" value="${item.id}">
                <input type="hidden" name="items[${item.id}][quantity]" value="${item.quantity}">
                <input type="hidden" name="items[${item.id}][unit_price]" value="${item.price}">
            `;
        });
        html += '</div>';
        cartContainer.innerHTML = html;
        submitBtn.disabled = false;
    }
    
    // Calculate totals
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const tax = subtotal * 0.10;
    const serviceCharge = subtotal * 0.05;
    const total = subtotal + tax + serviceCharge;
    
    document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
    document.getElementById('tax').textContent = '₹' + tax.toFixed(2);
    document.getElementById('serviceCharge').textContent = '₹' + serviceCharge.toFixed(2);
    document.getElementById('total').textContent = '₹' + total.toFixed(2);
}
</script>
@endsection
