@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Panier</div>

                    <div class="card-body text-center">
                        <h3>Votre panier est vide</h3>
                        <p>Vous n'avez pas encore ajouté de produits à votre panier.</p>
                        <a href="{{ route('produits.index') }}" class="btn btn-primary">
                            Parcourir les produits
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
