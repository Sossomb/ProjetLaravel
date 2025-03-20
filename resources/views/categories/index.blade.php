@extends('layouts.app')

@section('title', 'Gestion des Catégories')

@section('content')
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Gestion des Catégories</h1>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategorieModal">
                <i class="fas fa-plus-circle"></i> Nouvelle Catégorie
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($categories->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Libellé</th>
                            <th>Description</th>
                            <th>Produits</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($categories as $categorie)
                            <tr>
                                <td>{{ $categorie->id }}</td>
                                <td>{{ $categorie->libelle }}</td>
                                <td>{{ Str::limit($categorie->description, 50) }}</td>
                                <td>{{ $categorie->produits_count }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning edit-btn"
                                            data-id="{{ $categorie->id }}"
                                            data-libelle="{{ $categorie->libelle }}"
                                            data-description="{{ $categorie->description }}"
                                            data-bs-toggle="modal" data-bs-target="#editCategorieModal">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('categories.destroy', $categorie) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $categories->links() }}
        </div>
    @else
        <div class="alert alert-info">
            Aucune catégorie n'a été créée.
        </div>
    @endif

    <!-- Modal pour créer une nouvelle catégorie -->
    <div class="modal fade" id="createCategorieModal" tabindex="-1" aria-labelledby="createCategorieModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCategorieModalLabel">Nouvelle Catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('categories.store') }}" method="POST">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="libelle" class="form-label">Libellé</label>
                            <input type="text" class="form-control" id="libelle" name="libelle" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal pour modifier une catégorie -->
    <div class="modal fade" id="editCategorieModal" tabindex="-1" aria-labelledby="editCategorieModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategorieModalLabel">Modifier la Catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editCategorieForm" method="POST">
                    <div class="modal-body">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="edit_libelle" class="form-label">Libellé</label>
                            <input type="text" class="form-control" id="edit_libelle" name="libelle" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Configuration du modal de modification
                const editButtons = document.querySelectorAll('.edit-btn');
                const editForm = document.getElementById('editCategorieForm');
                const editLibelle = document.getElementById('edit_libelle');
                const editDescription = document.getElementById('edit_description');

                editButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const libelle = this.getAttribute('data-libelle');
                        const description = this.getAttribute('data-description');

                        editForm.action = `/categories/${id}`;
                        editLibelle.value = libelle;
                        editDescription.value = description;
                    });
                });

                // Gestion de la suppression
                const deleteForms = document.querySelectorAll('.delete-form');
                deleteForms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();

                        if (confirm('Êtes-vous sûr de vouloir supprimer cette catégorie?')) {
                            // Obtenir le jeton CSRF
                            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                            fetch(this.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    _method: 'DELETE'
                                })
                            })
                                .then(response => {
                                    if (response.redirected) {
                                        window.location.href = response.url;
                                        return;
                                    }

                                    return response.json();
                                })
                                .then(data => {
                                    if (data) {
                                        if (data.error) {
                                            alert(data.error);
                                        } else if (data.success) {
                                            alert(data.success);
                                            window.location.reload();
                                        }
                                    } else {
                                        window.location.reload();
                                    }
                                })
                                .catch(error => {
                                    console.error('Erreur:', error);
                                    window.location.reload();
                                });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
