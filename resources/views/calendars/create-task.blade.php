@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Ajouter une Tâche pour le {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('calendars.tasks.store', $date) }}">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Titre</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
        </div>
        <div class="mb-3">
            <label for="start_date" class="form-label">Date de début (jj/mm/aaaa --:-- --)</label>
            <input type="datetime-local" class="form-control" id="start_date" name="start_date" value="{{ \Carbon\Carbon::parse($date)->format('Y-m-d\TH:i') }}" readonly required>
        </div>
        <div class="mb-3">
            <label for="deadline" class="form-label">Date de fin (jj/mm/aaaa --:-- --)</label>
            <input type="datetime-local" class="form-control" id="deadline" name="deadline" value="{{ old('deadline') }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter la Tâche</button>
    </form>
</div>
@endsection
