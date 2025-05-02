<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid" style="background: #1a2a44; min-height: 100vh; color: white; padding: 20px;">
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }} - Veuillez contacter l'administrateur.
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <img src="{{ asset('images/telemonit-logo.png') }}" alt="TELEMONIT Logo" style="height: 40px;">
        </div>
        <div class="d-flex align-items-center">
            <a href="#" class="text-white me-3"><i class="fas fa-moon"></i></a>
            <img src="{{ Auth::user()->photoProfil ? asset('storage/' . Auth::user()->photoProfil) : asset('images/default-profile.png') }}" alt="Admin Photo" class="rounded-circle me-3" style="width: 40px; height: 40px;" onerror="this.src='{{ asset('images/default-profile.png') }}';">
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm rounded-pill">Déconnexion</button>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-dark text-white shadow-lg border-0 rounded-4 p-3">
                <div class="d-flex align-items-center">
                    <img src="{{ Auth::user()->photoProfil ? asset('storage/' . Auth::user()->photoProfil) : asset('images/default-profile.png') }}" alt="Admin Photo" class="rounded-circle me-3" style="width: 60px; height: 60px;" onerror="this.src='{{ asset('images/default-profile.png') }}';">
                    <div>
                        <h5 class="mb-0">{{ Auth::user()->nom ?? 'Admin' }} {{ Auth::user()->prenom ?? '' }}</h5>
                        <p class="mb-0 text-muted">{{ Auth::user()->email ?? 'email@example.com' }}</p>
                        <p class="mb-0 text-muted">{{ Auth::user()->telephone ?? '+216 ...' }}</p>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm rounded-pill">Modifier Profil</a>
                </div>
            </div>
        </div>
        <div class="col-md-9 text-end">
            <a href="{{ route('admin.teletravailleur.create') }}" class="btn btn-success rounded-pill px-4">Add profile</a>
            <!-- Ajout du bouton pour accéder au calendrier -->
            <a href="{{ route('calendars.index') }}" class="btn btn-primary rounded-pill px-4 ms-2">Accéder à Mon Calendrier</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h3 class="mb-4">Profiles</h3>
            @if ($teletravailleurs->isEmpty())
                <p class="text-muted">Aucun télétravailleur enregistré.</p>
            @else
                <div class="card bg-dark text-white shadow-lg border-0 rounded-4">
                    <div class="card-body p-0">
                        <table class="table table-dark table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Photo</th>
                                    <th>Name & Surname</th>
                                    <th>Total Hours/Day</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($teletravailleurs as $utilisateur)
                                    @if ($utilisateur->teletravailleur)
                                        @php
                                            $workingHours = $utilisateur->teletravailleur->workingHours->filter(function($session) {
                                                return \Carbon\Carbon::parse($session->start_time)->toDateString() === now()->toDateString();
                                            });

                                            $totalSeconds = 0;
                                            foreach ($workingHours as $session) {
                                                if ($session->stop_time) {
                                                    $totalSeconds += $session->total_seconds;
                                                } else {
                                                    $startTime = \Carbon\Carbon::parse($session->start_time);
                                                    $currentTime = now();
                                                    $seconds = $currentTime->diffInSeconds($startTime);

                                                    if ($session->pause_time && $session->resume_time) {
                                                        $pauseTime = \Carbon\Carbon::parse($session->pause_time);
                                                        $resumeTime = \Carbon\Carbon::parse($session->resume_time);
                                                        $pauseSeconds = $resumeTime->diffInSeconds($pauseTime);
                                                        $seconds -= $pauseSeconds;
                                                    } elseif ($session->pause_time) {
                                                        $pauseTime = \Carbon\Carbon::parse($session->pause_time);
                                                        $seconds = $pauseTime->diffInSeconds($startTime);
                                                    }
                                                    $totalSeconds += $seconds;
                                                }
                                            }

                                            $hours = floor($totalSeconds / 3600);
                                            $minutes = floor(($totalSeconds % 3600) / 60);
                                            $displayTime = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
                                            if ($totalSeconds == 0) {
                                                $displayTime = '0h 0m';
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $utilisateur->teletravailleur->id }}</td>
                                            <td>
                                                <img src="{{ $utilisateur->teletravailleur->photoProfil ? asset('storage/' . $utilisateur->teletravailleur->photoProfil) : asset('images/default-profile.png') }}" alt="Profile Photo" class="rounded-circle" style="width: 40px; height: 40px;" onerror="this.src='{{ asset('images/default-profile.png') }}';">
                                            </td>
                                            <td>{{ $utilisateur->nom ?? 'N/A' }} {{ $utilisateur->prenom ?? '' }}</td>
                                            <td>{{ $displayTime }}</td>
                                            <td>
                                                <span class="badge {{ $utilisateur->isOnline() ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $utilisateur->isOnline() ? 'Connected' : 'Disconnected' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.teletravailleur.details', $utilisateur->id) }}" class="btn btn-info btn-sm rounded-pill me-2">Voir Détails</a>
                                                <form action="{{ route('admin.teletravailleur.destroy', $utilisateur->teletravailleur->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce télétravailleur ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm rounded-pill">Remove profile</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
@endsection
