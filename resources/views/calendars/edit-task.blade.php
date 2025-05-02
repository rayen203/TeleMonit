<!-- resources/views/calendars/edit-task.blade.php -->
@extends('layouts.app')

@section('title', 'Modifier une Tâche')

@section('content')
    <div class="container mt-5">
        <h1>Modifier une Tâche</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('calendars.tasks.update', [$formattedDate, $task['id']]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Titre</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $task['title']) }}" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control">{{ old('description', $task['description'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label for="start_date">Date de début</label>
                <input type="datetime-local" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', \Carbon\Carbon::parse($task['start_date'])->format('Y-m-d\TH:i')) }}" required>
            </div>

            <div class="form-group">
                <label for="deadline">Date limite</label>
                <input type="datetime-local" name="deadline" id="deadline" class="form-control" value="{{ old('deadline', \Carbon\Carbon::parse($task['deadline'])->format('Y-m-d\TH:i')) }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Mettre à jour la Tâche</button>
            <a href="{{ route('calendars.index') }}" class="btn btn-secondary">Retour</a>
        </form>
    </div>
@endsection
