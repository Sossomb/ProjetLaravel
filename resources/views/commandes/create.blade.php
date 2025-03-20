@extends('layouts.app')

@section('title', 'Créer une Commande')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <h1>Créer une Commande</h1>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h2 class="card-title h5 mb-0">Sélection du Client</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('commandes.store') }}" method="POST" id="commande-form">
                @csrf

                <div class="mb-3">
                    <label for="client_id" class="form-label">Client</label>
                    <select class="form-select @error('client_id') is-invalid @enderror" id="client_id" name="client_id" required>
                        <option value="">Sélectionner un client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }} ({{ $client->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h2 class="card-title h5 mb-0">Sélection des Produits</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="products-table">
                    <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Quantité</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($produits as $produit)
                        <tr class="{{ $produit->stock <= 0 ? 'table-danger' : '' }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $produit->image ? asset('storage/'. $produit->image) : asset('images/placeholder.png') }}"
                                         style="width: 50px; height: 50px; object-fit: cover;" class="me-2 rounded" alt="{{ $produit->nom }}">
                                    <span>{{ $produit->nom }}</span>
                                </div>
                            </td>
                            <td>{{ $produit->categorie->libelle }}</td>
                            <td>{{ number_format($produit->prix, 2, ',', ' ') }} €</td>
                            <td>{{ $produit->stock }}</td>
                            <td width="120">
                                <input type="number" min="0" max="{{ $produit->stock }}" value="0"
                                       class="form-control product-qty" data-id="{{ $produit->id }}"
                                       data-price="{{ $produit->prix }}" data-max="{{ $produit->stock }}"
                                    {{ $produit->stock <= 0 ? 'disabled' : '' }}>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary add-product" data-id="{{ $produit->id }}"
                                    {{ $produit->stock <= 0 ? 'disabled' : '' }}>
                                    Ajouter
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h2 class="card-title h5 mb-0">Récapitulatif de la Commande</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="order-items">
                    <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr id="no-items">
                        <td colspan="5" class="text-center">Aucun produit sélectionné</td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th id="order-total">0,00 €</th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between">
                <a href="{{ route('commandes.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" form="commande-form" class="btn btn-success" id="submit-order" disabled>
                    Créer la commande
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let orderItems = [];
                let orderTotal = 0;

                // Add product to order
                document.querySelectorAll('.add-product').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.dataset.id;
                        const row = this.closest('tr');
                        const qtyInput = row.querySelector('.product-qty');
                        const qty = parseInt(qtyInput.value);

                        if (qty <= 0) {
                            alert('Veuillez sélectionner une quantité valide');
                            return;
                        }

                        const name = row.querySelector('td:first-child span').textContent;
                        const price = parseFloat(qtyInput.dataset.price);
                        const maxQty = parseInt(qtyInput.dataset.max);

                        // Check if product already in order
                        const existingItem = orderItems.find(item => item.id === id);
                        if (existingItem) {
                            const newQty = existingItem.qty + qty;
                            if (newQty > maxQty) {
                                alert(`Stock insuffisant. Maximum disponible: ${maxQty}`);
                                return;
                            }
                            existingItem.qty = newQty;
                            updateOrderTable();
                        } else {
                            orderItems.push({
                                id,
                                name,
                                price,
                                qty
                            });
                            updateOrderTable();
                        }

                        // Reset input
                        qtyInput.value = 0;
                    });
                });

                // Update order table
                function updateOrderTable() {
                    const tbody = document.querySelector('#order-items tbody');
                    const noItems = document.querySelector('#no-items');
                    const submitBtn = document.querySelector('#submit-order');

                    if (orderItems.length > 0) {
                        noItems.style.display = 'none';
                        submitBtn.disabled = false;

                        // Clear and rebuild table
                        tbody.innerHTML = '';
                        orderTotal = 0;

                        orderItems.forEach(item => {
                            const itemTotal = item.price * item.qty;
                            orderTotal += itemTotal;

                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                        <td>${item.name}</td>
                        <td>${item.price.toFixed(2).replace('.', ',')} €</td>
                        <td>${item.qty}</td>
                        <td>${itemTotal.toFixed(2).replace('.', ',')} CFA</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-item" data-id="${item.id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    `;
                            tbody.appendChild(tr);

                            // Add hidden inputs to form
                            const form = document.getElementById('commande-form');
                            const idInput = document.createElement('input');
                            idInput.type = 'hidden';
                            idInput.name = 'produits[]';
                            idInput.value = item.id;
                            form.appendChild(idInput);

                            const qtyInput = document.createElement('input');
                            qtyInput.type = 'hidden';
                            qtyInput.name = 'quantites[]';
                            qtyInput.value = item.qty;
                            form.appendChild(qtyInput);
                        });

                        // Update total
                        document.getElementById('order-total').textContent = orderTotal.toFixed(2).replace('.', ',') + ' €';

                        // Add event listeners to remove buttons
                        document.querySelectorAll('.remove-item').forEach(button => {
                            button.addEventListener('click', function() {
                                const id = this.dataset.id;
                                orderItems = orderItems.filter(item => item.id !== id);
                                updateOrderTable();
                            });
                        });
                    } else {
                        noItems.style.display = '';
                        tbody.innerHTML = '';
                        document.getElementById('order-total').textContent = '0,00 €';
                        submitBtn.disabled = true;

                        // Clear hidden inputs
                        const form = document.getElementById('commande-form');
                        form.querySelectorAll('input[name="produits[]"], input[name="quantites[]"]').forEach(input => {
                            input.remove();
                        });
                    }
                }
            });
        </script>
    @endpush
@endsection
