@extends('layouts.crud')

@section('title', 'Nueva Venta')

@section('content')
    <x-crud.page-header title="Punto de Venta" subtitle="Crear una nueva venta para cliente.">
        <x-slot:actions>
            <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="fade-slide">
        <form method="POST" action="{{ route('ventas.store') }}" role="form" id="venta-form">
            @csrf
            @include('venta.form')
        </form>
    </div>
@endsection

@push('styles')
<style>
    .pos-container {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 1.5rem;
        min-height: 700px;
        height: calc(100vh - 250px);
    }
    @media (max-width: 1200px) {
        .pos-container {
            grid-template-columns: 1fr;
            height: auto;
        }
        .products-section, .cart-section {
            height: auto;
            max-height: 600px;
        }
    }
    .products-section {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .cart-section {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
    }
    .categories-tabs {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .category-tab {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        background: #f3f4f6;
        color: #374151;
        cursor: pointer;
        white-space: nowrap;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
    }
    .category-tab.active {
        background: var(--title-red);
        color: white;
    }
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 1rem;
        overflow-y: auto;
        padding: 0.5rem;
    }
    .product-card {
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 120px;
    }
    .product-card:hover {
        border-color: var(--title-red);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .product-name {
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        color: #111827;
    }
    .product-price {
        color: var(--accent-green);
        font-weight: 800;
        font-size: 1.1rem;
    }
    .cart-items {
        flex: 1;
        overflow-y: auto;
        margin-bottom: 1.5rem;
    }
    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .cart-item-info {
        flex: 1;
    }
    .cart-item-name {
        font-weight: 600;
        display: block;
    }
    .cart-item-price {
        font-size: 0.85rem;
        color: #6b7280;
    }
    .cart-item-qty {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0 1rem;
    }
    .qty-btn {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        border: 1px solid #d1d5db;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .cart-item-total {
        font-weight: 700;
        width: 80px;
        text-align: right;
    }
    .cart-summary {
        border-top: 2px solid #f3f4f6;
        padding-top: 1rem;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    .summary-total {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--title-red);
    }
    .payment-method-btn.active {
        background-color: var(--title-red);
        color: white;
    }
</style>
@endpush
