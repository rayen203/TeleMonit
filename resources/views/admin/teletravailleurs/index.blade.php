@extends('layouts.app')

@section('content')
<div class="container-fluid" style="background: #1a2a44; min-height: 100vh; color: white; padding: 20px;">
    <h2 class="mb-4">Liste des Télétravailleurs</h2>

    @if ($teletravailleurs->isEmpty())
        <p class="text-muted">Aucun télétravailleur enregistré.</p>
    @else
        <div class="card bg-dark text-white shadow-lg border-0 rounded-4">
            <div class="card-body p-0">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Poste</th>
                            <th>Statut (Connexion)</th>
                            <th>Inactivité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="teletravailleurs-list">
                        @foreach($teletravailleurs as $teletravailleur)
                            <tr>
                                <td>{{ $teletravailleur->id }}</td>
                                <td>{{ $teletravailleur->utilisateur?->nom ?? 'N/A' }} {{ $teletravailleur->utilisateur?->prenom ?? '' }}</td>
                                <td>{{ $teletravailleur->utilisateur?->email ?? 'N/A' }}</td>
                                <td>{{ $teletravailleur->poste ?? 'N/A' }}</td>
                                <td>
                                    <span class="status-badge {{ $teletravailleur->utilisateur?->statut ? 'text-success' : 'text-danger' }}" id="status-{{ $teletravailleur->id }}">
                                        @if ($teletravailleur->utilisateur)
                                            @if ($teletravailleur->utilisateur->last_activity && now()->diffInMinutes($teletravailleur->utilisateur->last_activity) < 5)
                                                🟢 Actif
                                            @else
                                                🔴 Inactif
                                            @endif
                                        @else
                                            🔴 Inactif
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge {{ $teletravailleur->utilisateur?->isOnline() ? 'text-success' : 'text-danger' }}" id="online-{{ $teletravailleur->id }}">
                                        @if ($teletravailleur->utilisateur)
                                            {{ $teletravailleur->utilisateur->isOnline() ? 'Actif récemment' : 'Inactif depuis ' . ($teletravailleur->utilisateur->last_activity ? $teletravailleur->utilisateur->last_activity->diffForHumans() : 'N/A') }}
                                        @else
                                            Inactif
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.teletravailleur.destroy', $teletravailleur->id) }}" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce télétravailleur ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-pill">Remove profile</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<!-- CSS personnalisé -->
<style>
    .table-dark th, .table-dark td {
        border: none;
    }
    .table-dark tr:hover {
        background: rgba(255, 255, 255, 0.1);
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 5px;
    }
    .card {
        background: #2c3e50;
    }
    .text-success {
        color: #28a745 !important;
    }
    .text-danger {
        color: #dc3545 !important;
    }
</style>

<!-- Script pour mise à jour des statuts avec gestion des erreurs -->
<script>
    function updateStatus() {
        fetch('/admin/teletravailleurs/status')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau ou route non trouvée');
                }
                return response.json();
            })
            .then(data => {
                if (data.teletravailleurs) {
                    data.teletravailleurs.forEach(teletravailleur => {
                        const statusElement = document.getElementById(`status-${teletravailleur.id}`);
                        const onlineElement = document.getElementById(`online-${teletravailleur.id}`);
                        if (statusElement && onlineElement) {
                            // Vérification basée sur last_activity pour une meilleure précision
                            const isActive = teletravailleur.last_activity && (new Date() - new Date(teletravailleur.last_activity)) / 60000 < 5; // Actif si moins de 5 minutes
                            statusElement.textContent = isActive ? '🟢 Actif' : '🔴 Inactif';
                            statusElement.className = `status-badge ${isActive ? 'text-success' : 'text-danger'}`;
                            onlineElement.textContent = teletravailleur.isOnline ? 'Actif récemment' : 'Inactif depuis ' + (teletravailleur.last_activity || 'N/A');
                            onlineElement.className = `status-badge ${teletravailleur.isOnline ? 'text-success' : 'text-danger'}`;
                        }
                    });
                } else {
                    console.warn('Aucune donnée de télétravailleurs reçue');
                }
            })
            .catch(error => {
                console.error('Erreur lors de la mise à jour des statuts:', error.message);
                document.querySelectorAll('[id^="status-"]').forEach(element => {
                    element.textContent = '🔴 Inactif (Erreur API)';
                    element.className = 'status-badge text-danger';
                });
                document.querySelectorAll('[id^="online-"]').forEach(element => {
                    element.textContent = 'Inactif (Erreur API)';
                    element.className = 'status-badge text-danger';
                });
            });
    }

    updateStatus();
    setInterval(updateStatus, 10000);
</script>
@endsection
