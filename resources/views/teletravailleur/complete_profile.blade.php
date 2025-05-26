
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Compléter votre profil</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('teletravailleur.update_profile') }}" enctype="multipart/form-data">
        @csrf


        <div class="mb-3">
            <label for="adresse" class="form-label">Adresse</label>
            <input type="text" class="form-control" id="adresse" name="adresse" value="{{ old('adresse', $teletravailleur->adresse) }}">
        </div>

        <div class="mb-3">
            <label for="num_tel" class="form-label">Numéro de téléphone</label>
            <input type="text" class="form-control" id="num_tel" name="num_tel" value="{{ old('num_tel', $teletravailleur->num_tel) }}">
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
</div>
@endsection
