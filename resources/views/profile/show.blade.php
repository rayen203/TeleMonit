@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Profil de l'utilisateur</h2>

    <div class="card mb-4">
        <div class="card-header">Informations utilisateur</div>
        <div class="card-body">
            <p><strong>Nom:</strong> {{ $user->nom }}</p>
            <p><strong>Prénom:</strong> {{ $user->prenom }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Créé le:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
        </div>
    </div>

    @if($teletravailleur)
        <div class="card">
            <div class="card-header">Informations du télétravailleur</div>
            <div class="card-body">
                <p><strong>CIN:</strong> {{ $teletravailleur->CIN }}</p>
                <p><strong>Téléphone:</strong> {{ $teletravailleur->telephone }}</p>
                <p><strong>Adresse:</strong> {{ $teletravailleur->adresse }}</p>
                @if($teletravailleur->photoProfil)
                    <p><strong>Photo:</strong> <img src="{{ Storage::url($teletravailleur->photoProfil) }}" alt="Photo" style="max-width: 100px;"></p>
                @endif
            </div>
        </div>
    @endif

    <a href="{{ route('profile.edit') }}" class="btn btn-primary mt-4">Modifier le Profil</a>
</div>
@endsection
