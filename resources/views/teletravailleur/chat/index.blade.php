<!-- resources/views/teletravailleur/chat/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Chat avec le chatbot</h2>

    <!-- Affichage des messages précédents -->
    <div class="card mb-4">
        <div class="card-header">Historique de la conversation</div>
        <div class="card-body">
            @foreach($chatbot->historique as $message)
                <div class="{{ $message['is_from_teletravailleur'] ? 'text-end' : 'text-start' }}">
                    <p><strong>{{ $message['is_from_teletravailleur'] ? 'Vous' : 'Chatbot' }}:</strong> {{ $message['message'] }}</p>
                    <small>{{ $message['date'] }}</small>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Formulaire pour envoyer un message -->
    <form method="POST" action="{{ route('teletravailleur.chat.send') }}">
        @csrf
        <div class="mb-3">
            <label for="message" class="form-label">Votre message</label>
            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
</div>
@endsection
