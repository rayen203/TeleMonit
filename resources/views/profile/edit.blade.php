@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Modifier le Profil</h2>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <!-- Champs communs pour tous les utilisateurs -->
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom', auth()->user()->nom) }}" required>
        </div>

        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" value="{{ old('prenom', auth()->user()->prenom) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
        </div>

        <!-- Champs spécifiques aux télétravailleurs -->
        @if(auth()->user()->teletravailleur)
            <div class="mb-3">
                <label for="cin" class="form-label">CIN</label>
                <input type="text" class="form-control" id="cin" name="cin" value="{{ old('cin', auth()->user()->teletravailleur->cin ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="telephone" class="form-label">Numéro de téléphone</label>
                <input type="text" class="form-control" id="telephone" name="telephone" value="{{ old('telephone', auth()->user()->teletravailleur->telephone ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="adresse" class="form-label">Adresse</label>
                <input type="text" class="form-control" id="adresse" name="adresse" value="{{ old('adresse', auth()->user()->teletravailleur->adresse ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="photoProfil" class="form-label">Photo de Profil</label>
                <input type="file" class="form-control" id="photoProfil" name="photoProfil">
                @if(auth()->user()->photoProfil)
                    <img src="{{ Storage::url(auth()->user()->photoProfil) }}" alt="Photo de profil actuelle" style="max-width: 100px; margin-top: 10px;">
                @endif
            </div>

        @endif



        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
</div>
@endsection
