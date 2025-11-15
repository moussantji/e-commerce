<div class="relative">
    <button 
        @click="open = !open" 
        class="flex items-center text-gray-700 hover:text-gray-900 focus:outline-none"
    >
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        @if($itemsCount > 0)
            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                {{ $itemsCount }}
            </span>
        @endif
    </button>

    <div 
        x-show="open" 
        @click.away="open = false"
        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl overflow-hidden z-50"
        style="display: none;"
    >
        <div class="p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Votre panier</h3>
            
            @if($itemsCount > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($cart->products as $product)
                        <div class="py-3 flex items-center">
                            <div class="flex-shrink-0 h-16 w-16 rounded-md overflow-hidden">
                                @if(isset($product->images[0]))
                                    <img 
                                        src="{{ asset('storage/' . $product->images[0]) }}" 
                                        alt="{{ $product->name }}"
                                        class="h-full w-full object-cover object-center"
                                    >
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between text-base font-medium text-gray-900">
                                    <h3>{{ $product->name }}</h3>
                                    <p class="ml-4">{{ $this->formatFcfa($product->pivot->unit_price) }}</p>
                                </div>
                                <div class="flex items-center mt-1">
                                    <span class="text-sm text-gray-500">Qté: </span>
                                    <input 
                                        type="number" 
                                        min="1" 
                                        value="{{ $product->pivot->quantity }}"
                                        wire:change="updateQuantity({{ $product->id }}, $event.target.value)"
                                        class="ml-2 w-16 px-2 py-1 border rounded text-sm"
                                    >
                                    <button 
                                        wire:click="removeFromCart({{ $product->id }})"
                                        class="ml-2 text-red-500 hover:text-red-700"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 border-t border-gray-200 pt-4">
                    <div class="flex justify-between text-base font-medium text-gray-900">
                        <p>Total</p>
                        <p>{{ $this->formatFcfa($total) }}</p>
                    </div>
                    <div class="mt-4">
                        <a 
                            href="{{ route('checkout') }}" 
                            class="flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                        >
                            Commander
                        </a>
                    </div>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Votre panier est vide</p>
                <div class="mt-4">
                    <a 
                        href="{{ route('products') }}" 
                        class="flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                    >
                        Voir les produits
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function formatFcfa(amount) {
        return new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
    }
    
    document.addEventListener('livewire:load', function () {
        window.livewire.hook('message.processed', (message, component) => {
            // Mettre à jour le compteur dans la barre de navigation
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = component.entangle('itemsCount').value;
            }
        });
    });
</script>
@endpush
