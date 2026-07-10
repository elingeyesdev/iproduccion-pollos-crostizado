<div class="pos-container">
    <!-- Sección de Productos -->
    <div class="products-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 fw-bold"><i class="fa-solid fa-utensils me-2 text-danger"></i>Productos</h4>
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" id="search-product" class="form-control border-start-0" placeholder="Buscar producto...">
            </div>
        </div>

        <div class="categories-tabs" id="categories-tabs">
            <button type="button" class="category-tab active" data-id="all">Todos</button>
            @foreach($tipoProductos as $tipo)
                <button type="button" class="category-tab" data-id="{{ $tipo->id }}">{{ $tipo->nombre }}</button>
            @endforeach
        </div>

        <div class="products-grid" id="products-grid">
            @foreach($productos as $producto)
                <div class="product-card" data-id="{{ $producto->id }}" data-category="{{ $producto->tipo_producto_id }}" data-name="{{ $producto->nombre }}" data-price="{{ $producto->precio }}">
                    <div class="product-name">{{ $producto->nombre }}</div>
                    <div class="product-price">Bs {{ number_format($producto->precio, 2) }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Sección de Carrito -->
    <div class="cart-section">
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="cliente_id" class="form-label fw-bold mb-0">Cliente</label>
                <a href="{{ route('clientes.create') }}" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener noreferrer">
                    <i class="fa-solid fa-user-plus me-1"></i> Crear cliente nuevo
                </a>
            </div>
            <select name="cliente_id" id="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                <option value="">Seleccionar cliente...</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" @selected(old('cliente_id', $venta?->cliente_id) == $cliente->id)>{{ $cliente->nombre }}</option>
                @endforeach
            </select>
            @error('cliente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="cart-items" id="cart-items">
            <!-- Items added via JS -->
            <div class="text-center text-muted mt-5 py-5" id="empty-cart-msg">
                <i class="fa-solid fa-cart-shopping fa-3x mb-3 opacity-25"></i>
                <p>El carrito está vacío</p>
            </div>
        </div>

        <div class="cart-summary">
            <div class="mb-3">
                <label class="form-label fw-bold">Pagos</label>
                <div class="row g-2">
                    <div class="col-6">
                        <label for="pago_efectivo" class="form-label mb-1">Efectivo (Bs)</label>
                        <input type="number" step="0.01" min="0" name="pago_efectivo" id="pago_efectivo" class="form-control text-end" value="0">
                    </div>
                    <div class="col-6">
                        <label for="pago_qr" class="form-label mb-1">QR (Bs)</label>
                        <input type="number" step="0.01" min="0" name="pago_qr" id="pago_qr" class="form-control text-end" value="0">
                    </div>
                </div>
                <div class="small mt-1" id="payment-status"></div>
            </div>

            <div class="summary-row">
                <span class="fw-bold">Subtotal</span>
                <span id="subtotal-display">Bs 0.00</span>
            </div>
            <div class="summary-row">
                <span class="fw-bold">IGV (18%)</span>
                <span id="igv-display">Bs 0.00</span>
            </div>
            <div class="summary-row align-items-center">
                <span class="fw-bold">Descuento</span>
                <div class="input-group input-group-sm" style="width: 120px;">
                    <span class="input-group-text">Bs</span>
                    <input type="number" name="descuento" id="descuento-input" class="form-control text-end" value="0" min="0" step="0.01">
                </div>
            </div>
            <div class="summary-row mt-2 pt-2 border-top">
                <span class="h4 fw-bold mb-0">Total</span>
                <span class="summary-total" id="total-display">Bs 0.00</span>
            </div>

            <input type="hidden" name="total" id="total-input" value="0">
            <input type="hidden" name="estado" value="1">

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-danger btn-lg py-3 fw-bold" id="btn-submit" disabled>
                    <i class="fa-solid fa-cash-register me-2"></i>FINALIZAR VENTA
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Plantilla oculta para items del carrito -->
<template id="cart-item-template">
    <div class="cart-item">
        <div class="cart-item-info">
            <span class="cart-item-name"></span>
            <span class="cart-item-price"></span>
        </div>
        <div class="cart-item-qty">
            <button type="button" class="qty-btn minus-btn"><i class="fa-solid fa-minus fa-xs"></i></button>
            <span class="qty-val">1</span>
            <button type="button" class="qty-btn plus-btn"><i class="fa-solid fa-plus fa-xs"></i></button>
        </div>
        <div class="cart-item-total"></div>
        <button type="button" class="btn btn-link text-danger p-0 ms-2 remove-btn"><i class="fa-solid fa-trash-can"></i></button>
        
        <!-- Hidden inputs for form submission -->
        <input type="hidden" name="productos[]" class="input-id">
        <input type="hidden" name="cantidades[]" class="input-qty">
        <input type="hidden" name="precios[]" class="input-price">
    </div>
</template>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productsGrid = document.getElementById('products-grid');
        const cartItemsContainer = document.getElementById('cart-items');
        const emptyCartMsg = document.getElementById('empty-cart-msg');
        const totalDisplay = document.getElementById('total-display');
        const totalInput = document.getElementById('total-input');
        const descuentoInput = document.getElementById('descuento-input');
        const subtotalDisplay = document.getElementById('subtotal-display');
        const igvDisplay = document.getElementById('igv-display');
        const btnSubmit = document.getElementById('btn-submit');
        const searchInput = document.getElementById('search-product');
        const categoryTabs = document.querySelectorAll('.category-tab');
        const cartItemTemplate = document.getElementById('cart-item-template');
        const pagoEfectivoInput = document.getElementById('pago_efectivo');
        const pagoQrInput = document.getElementById('pago_qr');
        const paymentStatus = document.getElementById('payment-status');

        let cart = [];

        // Manejo de descuento
        descuentoInput.addEventListener('input', () => {
            renderCart();
        });

        pagoEfectivoInput.addEventListener('input', () => renderCart());
        pagoQrInput.addEventListener('input', () => renderCart());

        // Filtro por categorías
        categoryTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                categoryTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                const categoryId = tab.dataset.id;
                const products = document.querySelectorAll('.product-card');
                
                products.forEach(p => {
                    if (categoryId === 'all' || p.dataset.category === categoryId) {
                        p.style.display = 'flex';
                    } else {
                        p.style.display = 'none';
                    }
                });
            });
        });

        // Búsqueda de productos
        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            const products = document.querySelectorAll('.product-card');
            
            products.forEach(p => {
                const name = p.dataset.name.toLowerCase();
                if (name.includes(term)) {
                    p.style.display = 'flex';
                } else {
                    p.style.display = 'none';
                }
            });
        });

        // Agregar al carrito
        productsGrid.addEventListener('click', (e) => {
            const card = e.target.closest('.product-card');
            if (!card) return;

            const id = card.dataset.id;
            const name = card.dataset.name;
            const price = parseFloat(card.dataset.price);

            const existingItem = cart.find(item => item.id === id);

            if (existingItem) {
                existingItem.qty++;
            } else {
                cart.push({ id, name, price, qty: 1 });
            }

            renderCart();
        });

        function renderCart() {
            cartItemsContainer.innerHTML = '';
            
            if (cart.length === 0) {
                cartItemsContainer.appendChild(emptyCartMsg);
                btnSubmit.disabled = true;
                totalDisplay.textContent = 'Bs 0.00';
                subtotalDisplay.textContent = 'Bs 0.00';
                igvDisplay.textContent = 'Bs 0.00';
                paymentStatus.textContent = 'Agrega productos para habilitar el pago.';
                paymentStatus.className = 'small mt-1 text-muted';
                totalInput.value = 0;
                descuentoInput.value = 0;
                return;
            }

            let subtotalTotal = 0;

            cart.forEach((item, index) => {
                const clone = cartItemTemplate.content.cloneNode(true);
                const row = clone.querySelector('.cart-item');
                
                clone.querySelector('.cart-item-name').textContent = item.name;
                clone.querySelector('.cart-item-price').textContent = `Bs ${item.price.toFixed(2)}`;
                clone.querySelector('.qty-val').textContent = item.qty;
                
                const itemTotal = item.price * item.qty;
                subtotalTotal += itemTotal;
                clone.querySelector('.cart-item-total').textContent = `Bs ${itemTotal.toFixed(2)}`;

                // Inputs para el form
                clone.querySelector('.input-id').value = item.id;
                clone.querySelector('.input-qty').value = item.qty;
                clone.querySelector('.input-price').value = item.price;

                // Eventos de botones
                clone.querySelector('.plus-btn').addEventListener('click', () => {
                    item.qty++;
                    renderCart();
                });

                clone.querySelector('.minus-btn').addEventListener('click', () => {
                    if (item.qty > 1) {
                        item.qty--;
                        renderCart();
                    }
                });

                clone.querySelector('.remove-btn').addEventListener('click', () => {
                    cart.splice(index, 1);
                    renderCart();
                });

                cartItemsContainer.appendChild(clone);
            });

            // El subtotal ya incluye el IGV en el precio de venta usualmente, 
            // pero si queremos mostrarlo desglosado:
            const igv = subtotalTotal * 0.18;
            const subtotalSinIgv = subtotalTotal - igv;
            
            const descuento = parseFloat(descuentoInput.value) || 0;
            const total = subtotalTotal - descuento;

            subtotalDisplay.textContent = `Bs ${subtotalTotal.toFixed(2)}`;
            igvDisplay.textContent = `Bs ${igv.toFixed(2)}`;
            totalDisplay.textContent = `Bs ${Math.max(0, total).toFixed(2)}`;
            totalInput.value = Math.max(0, total).toFixed(2);

            const pagoEfectivo = parseFloat(pagoEfectivoInput.value) || 0;
            const pagoQr = parseFloat(pagoQrInput.value) || 0;
            const pagoTotal = pagoEfectivo + pagoQr;
            const diferencia = pagoTotal - Math.max(0, total);

            if (pagoTotal <= 0) {
                btnSubmit.disabled = true;
                paymentStatus.textContent = 'Debes registrar un pago en efectivo, QR o ambos.';
                paymentStatus.className = 'small mt-1 text-danger';
                return;
            }

            if (Math.abs(diferencia) > 0.01) {
                btnSubmit.disabled = true;
                paymentStatus.textContent = `La suma de pagos debe ser igual al total. Diferencia: Bs ${diferencia.toFixed(2)}`;
                paymentStatus.className = 'small mt-1 text-danger';
                return;
            }

            btnSubmit.disabled = false;
            paymentStatus.textContent = 'Pago validado.';
            paymentStatus.className = 'small mt-1 text-success';
        }
    });
</script>
@endpush